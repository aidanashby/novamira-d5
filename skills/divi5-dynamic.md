Tested on Divi 5.15.1 with ACF 6.8.10 (14 October 2026), using the `movie` post type: a Loop Builder page and a Theme Builder template for single movies, both checked in a browser. Load `divi5-core` first.

## Dynamic content values

A dynamic value replaces a plain value anywhere a module takes text, a URL or an image `src`:

```php
$dc = fn(string $name, array $settings = []) => '$variable(' . wp_json_encode(['type' => 'content',
    'value' => ['name' => $name, 'settings' => (object) $settings]]) . ')$';
```

List the names this site offers (read only): `$ns = implode(chr(92), ['ET', 'Builder', 'Packages', 'Module', 'Layout', 'Components', 'DynamicContent', 'DynamicContentOptions']); return array_keys($ns::get_options($post_id, 'edit'));`. An option's `fields` are its allowed settings.

Rendered here: `post_title`, `post_featured_image`, `post_meta_key`, `loop_post_title`, `loop_post_link`, `loop_post_featured_image`, `loop_post_meta_key_manual_custom_field`. The others are listed by the registry but not rendered yet.

| Where | Name | Settings |
|---|---|---|
| Current post (single pages, Theme Builder bodies) | `post_title`, `post_excerpt`, `post_date`, `post_link_url`, `post_featured_image` | `before`, `after`; image: `thumbnail_size` (`large`, `medium`, `thumbnail`, `full`) |
| Current post custom field or ACF field | `post_meta_key` | `select_meta_key`: `custom_meta_<field name>`; `enable_html`: `on` for WYSIWYG fields |
| Inside a loop | `loop_post_title`, `loop_post_link`, `loop_post_featured_image`, `loop_post_excerpt`, `loop_post_date` | as above |
| Inside a loop, custom field or ACF field | `loop_post_meta_key_manual_custom_field` | `select_loop_meta_key`: `loop_post_meta_key_<field name>`; `enable_html`: `on` for WYSIWYG |

Post content in a Theme Builder body: use the `divi/post-content` module (no attributes beyond `builderVersion`), not a dynamic value.

Verified field types: text, WYSIWYG (with `enable_html` on, HTML kept), image (an ACF image field works as an image `src`), URL, and the subfields of an ACF group, which are flat meta named `<group>_<subfield>`.

**Dates.** Divi's formatter treats a digits-only value as a Unix timestamp and parses anything else with `strtotime()`. So:

- An ACF **Date Picker** stores `Ymd` (`20261017`), which Divi misreads as 1970 when a `date_format` is set. Ask the user to use a **Date Time Picker**, which stores `Y-m-d H:i:s`.
- Inside a loop, Divi reads the stored meta, so a Date Time Picker formats correctly (verified: `date_format` `j M, Y` gave "16 Oct, 2026").
- Outside a loop (`post_meta_key`), Divi reads the ACF **Return Format**. A slashed format such as `d/m/Y g:i a` cannot be parsed, so the raw text shows. Ask the user to set the field's Return Format to `Y-m-d H:i:s`.
- Date settings: `date_format` (`j M, Y`, `F d, Y`, `default` and others) or `custom` with `custom_date_format`.

## Loop Builder

Any module or group repeats once per result when `module.advanced.loop` is set:

```php
'module' => ['advanced' => ['loop' => $d([
    'enable' => 'on', 'loopId' => 'movies_cards', 'queryType' => 'post_types',
    'subTypes' => [['value' => 'movie', 'label' => 'Movies']],
    'postPerPage' => '3', 'orderBy' => 'date', 'order' => 'ASC'])]]
```

- Put the loop on a `divi/group` to repeat a card made of several modules (image, heading, text, button), each using `loop_post_*` values. A loop on a single module (a blurb) repeats that module.
- For a grid of any length, loop the column instead: one row with `module.decoration.layout` `flexWrap` `wrap` on desktop and phone, holding one `divi/column` that carries the loop, with `flexType` `8_24` on desktop and `24_24` on phone. Each result becomes a column, so three per line on desktop and stacked on phone (verified in a browser at 1280px and 447px).
- `loopId` is any unique string on the page. It is also the pagination parameter: `?<loopId>=2` shows page 2.
- Pagination (verified on the Movies archive): add a `divi/post-nav` module with `module.advanced.targetLoop` `{"desktop":{"value":"<loopId>"}}`. With `postPerPage` `2` and three movies it showed two movies and a link to `?movie_archive=2`, and page 2 showed the third with a link back.
- `excludeCurrentPost` `"on"` with `orderBy` `"title"` (verified): a "More movies" loop in a single-movie Theme Builder body lists the other movies in title order and leaves out the one being viewed.
- Filter by custom field with `metaQuery`, a list of `{"key","value","compare","type"}` (verified on SQLite):
  - `[{"key":"group_date_field","value":"2026-11-01 00:00:00","compare":">=","type":"DATETIME"}]` gave the two later movies.
  - `[{"key":"text_field","value":"Second","compare":"LIKE"}]` gave the one match.
  - `BETWEEN` takes `"from,to"` in `value` (comma-separated); `IN` and `NOT IN` take a comma list.
  - Compares: `=`, `!=`, `>`, `>=`, `<`, `<=`, `LIKE`, `NOT LIKE`, `IN`, `NOT IN`, `BETWEEN`, `NOT BETWEEN`, `EXISTS`, `NOT EXISTS`, `REGEXP`. Types: `NUMERIC`, `DATE`, `DATETIME`, `DECIMAL`, `CHAR` and others.
  - Several items are always joined with OR, never AND (Divi sets the relation). For a range use one `BETWEEN` item. Two different custom-field conditions cannot both be required; a custom field plus a taxonomy term can (below).
  - `value` is fixed text, so "from today" loops need the date written in, and do not move on by themselves.
- Filter by taxonomy term with `includePostWithSpecificTerms` or `excludePostWithSpecificTerms`, a list of `{"categoryId":"<taxonomy>","selectedOptions":[{"value":"<term ID>","label":"<name>"}]}` (verified with `genre`). Several terms in one taxonomy match any of them. A term filter and a `metaQuery` together must both match (verified: Sci-fi and released on or after 1 December gave one movie), so combine them for "genre and date" listings.
- Show a post's terms with `loop_post_terms` and settings `taxonomy_type` (the taxonomy name; the default is `category`), `separator` (`", "`) and `links` (`on` links each term to its archive).
- Other keys Divi reads: `postOffset`, `ignoreStickysPost`, `includeSpecificPosts`, `excludeSpecificPosts`, `search` (not tested here).
- Use published posts for loop tests; Divi's query is expected to skip drafts (not tested).

Check: every card shows a different post, no `$variable(` text is left in the HTML, and images and links differ per item.

## Custom post types

- Read the type and its ACF fields first: `get_post_type_object($type)`, `acf_get_field_groups(['post_type' => $type])`, `acf_get_fields($group_key)`.
- Set ACF values with `update_field($name, $value, $post_id)` (a group takes an array of subfields), and check with `get_fields($post_id)`.
- Divi's builder may not be enabled for the type (`et_builder_get_enabled_builder_post_types()`). Theme Builder templates and loops still work for it.
- For a single-post layout for a type, see `divi5-theme-builder` (assignment `singular:post_type:<type>:all`).

Adapted in part from divilovewp/divi5-skill (MIT, copyright 2026 Shashank Gupta, divilove.com), verified and rewritten for Divi 5.15.1.
