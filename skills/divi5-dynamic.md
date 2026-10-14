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

**Date limit:** an ACF date inside a group is stored as `Ymd` (`20261017`). Divi does not know it is a date, so a `date_format` setting reads it as a Unix timestamp and shows 1970. Show it unformatted, or ask the user to make the date a top-level ACF field (not tested here).

## Loop Builder

Any module or group repeats once per result when `module.advanced.loop` is set:

```php
'module' => ['advanced' => ['loop' => $d([
    'enable' => 'on', 'loopId' => 'movies_cards', 'queryType' => 'post_types',
    'subTypes' => [['value' => 'movie', 'label' => 'Movies']],
    'postPerPage' => '3', 'orderBy' => 'date', 'order' => 'ASC'])]]
```

- Put the loop on a `divi/group` to repeat a card made of several modules (image, heading, text, button), each using `loop_post_*` values. A loop on a single module (a blurb) repeats that module.
- `loopId` is any unique string on the page.
- Other keys Divi reads: `postOffset`, `excludeCurrentPost`, `ignoreStickysPost`, `includeSpecificPosts`, `excludeSpecificPosts`, `includePostWithSpecificTerms`, `excludePostWithSpecificTerms`, `metaQuery`, `search` (not tested here).
- Use published posts for loop tests; Divi's query is expected to skip drafts (not tested).

Check: every card shows a different post, no `$variable(` text is left in the HTML, and images and links differ per item.

## Custom post types

- Read the type and its ACF fields first: `get_post_type_object($type)`, `acf_get_field_groups(['post_type' => $type])`, `acf_get_fields($group_key)`.
- Set ACF values with `update_field($name, $value, $post_id)` (a group takes an array of subfields), and check with `get_fields($post_id)`.
- Divi's builder may not be enabled for the type (`et_builder_get_enabled_builder_post_types()`). Theme Builder templates and loops still work for it.
- For a single-post layout for a type, see `divi5-theme-builder` (assignment `singular:post_type:<type>:all`).

Adapted in part from divilovewp/divi5-skill (MIT, copyright 2026 Shashank Gupta, divilove.com), verified and rewritten for Divi 5.15.1.
