Tested on Divi 5.15.1, WordPress 7.1.3 (14 October 2026). If `ET_BUILDER_VERSION` differs, treat version-sensitive rules as "verify on this site".

## Ground rules

- Divi 5 is the only builder here. Do not ask which builder to use. Do not load `novamira-design` or `custom-theme-build` for Divi page work, and never create, save or activate a Novamira design unless asked.
- Use native Divi modules. No Code module, injected HTML, CSS or JavaScript for layout.
- Build new pages as drafts. Publish a new or draft page only when the user asks (`wp_update_post(['ID' => $id, 'post_status' => 'publish'])`), then check it on the front end. Never delete, trash or unpublish a page. Never overwrite an existing page; copy it to a new draft instead, unless the user asks for an in-place edit.
- Do not write Divi components unless asked. Global colours, variables (every type) and presets: use existing ones first; you may propose a new or changed one yourself, and write it only after the user agrees (`divi5-styling`, `divi5-presets`).
- New website, new set of pages, design direction or redesign: load `divi5-design-system` first. It may require a design showcase and the user's approval before other pages are built.
- Ask at most one question, and only when the brief is ambiguous. Otherwise build, then report.

## How Divi 5 stores a page

`post_content` is WordPress block markup:

- Everything sits in one `divi/placeholder` block (no attributes).
- Hierarchy: placeholder, `divi/section`, `divi/row`, `divi/column`, then modules. Never put a module directly in a section or row. A column may also hold `divi/group` or a nested row.
- Every block except the placeholder carries `"builderVersion"` set to `ET_BUILDER_VERSION`. Read it at runtime, never type a literal. Divi only migrates blocks stamped with an older version, so a current stamp means the shape must already be current.
- Attributes are JSON with responsive values: `{"desktop":{"value":...},"tablet":{"value":...},"phone":{"value":...}}`. Always set `desktop`. Add `tablet` or `phone` only where they differ; desktop values cascade down.
- Text values are HTML: `&amp;`, `<strong>`, `<p>`. Do not pre-escape. The serialiser escapes `&`, `"`, `<`, `>` as JSON unicode escapes, exactly as the Visual Builder does.

## Build markup with serialize_blocks()

Never hand-write the markup string. Build PHP arrays and serialise them with WordPress core; this reproduces the builder's storage byte for byte.

```php
$v = ET_BUILDER_VERSION;
$b = function (string $name, array $attrs = [], array $children = []) use ($v) {
    $attrs['builderVersion'] = $v;
    return ['blockName' => $name, 'attrs' => $attrs, 'innerBlocks' => $children,
            'innerHTML' => '', 'innerContent' => array_fill(0, count($children), null)];
};
$d = fn($value) => ['desktop' => ['value' => $value]];

$heading = $b('divi/heading', ['title' => ['innerContent' => $d('Welcome'),
    'decoration' => ['font' => ['font' => $d(['headingLevel' => 'h1'])]]]]);
$text    = $b('divi/text', ['content' => ['innerContent' => $d('<p>Body text &amp; more.</p>')]]);
$button  = $b('divi/button', ['button' => ['innerContent' => $d(['text' => 'Contact us', 'linkUrl' => '/contact/'])]]);

$col = fn(string $w, array $kids) => $b('divi/column', ['module' => ['decoration' => ['sizing' =>
    ['desktop' => ['value' => ['flexType' => $w]], 'phone' => ['value' => ['flexType' => '24_24']]]]]], $kids);
$row = fn(string $layout, array $cols) => $b('divi/row', ['module' => [
    'advanced' => ['flexColumnStructure' => $d($layout)],
    'decoration' => ['layout' => ['desktop' => ['value' => ['flexWrap' => 'nowrap']],
                                  'phone' => ['value' => ['flexWrap' => 'wrap']]]]]], $cols);

$hero    = $row('equal-columns_1', [$col('24_24', [$heading, $text, $button])]);
$section = $b('divi/section', [], [$hero]);
$content = serialize_blocks([['blockName' => 'divi/placeholder', 'attrs' => [],
    'innerBlocks' => [$section], 'innerHTML' => '', 'innerContent' => [null]]]);
```

### Rows and columns

Use Divi's own layout templates (from the builder's column picker). Set the row's `module.advanced.flexColumnStructure` to the layout name and its `module.decoration.layout` to `{"desktop":{"value":{"flexWrap":"nowrap"}},"phone":{"value":{"flexWrap":"wrap"}}}`, then give each column `module.decoration.sizing` `{"desktop":{"value":{"flexType":"<width>"}},"phone":{"value":{"flexType":"24_24"}}}`, so columns stack on phone.

| Layout name | Column widths (desktop) |
|---|---|
| `equal-columns_1` | `24_24` |
| `equal-columns_2` | `12_24` x2 |
| `equal-columns_3` | `8_24` x3 |
| `equal-columns_4` | `6_24` x4 |
| `offset-columns_5` / `_6` | `16_24` + `8_24` / `8_24` + `16_24` |
| `offset-columns_9` / `_10` | `18_24` + `6_24` / `6_24` + `18_24` |
| `offset-columns_11` | `6_24` + `12_24` + `6_24` |

- The number of `divi/column` children must match the layout.
- `module.advanced.columnStructure` (row) and `module.advanced.type` (column) are legacy. The builder keeps writing `4_4` there whatever the column count, and rows render without them. Omit them in new rows; keep them when copying.
- A row with several `24_24` columns and `nowrap` (what the builder makes when the user adds columns one by one) shows them side by side but never stacks. Do not copy that form for new rows.

## Choose the module the user names

Use the native module for each thing the user asks for, never a heading and text stand-in: blurb `divi/blurb`, call to action `divi/cta`, image `divi/image`, FAQ or accordion `divi/accordion`, tabs `divi/tabs`, testimonial `divi/testimonial`, contact form `divi/contact-form`. For a module not shown below, read its `module.json` (path at the end of this skill) or copy it from a builder-saved page.

Verified shapes (from builder-saved pages on 5.15.1):

- Blurb: `{"title":{"innerContent":{"desktop":{"value":{"text":"Title"}}}},"content":{"innerContent":{"desktop":{"value":"<p>Body</p>"}}},"imageIcon":{"innerContent":{"desktop":{"value":{"src":"<image URL>"}}}}}`. The title value is an object with `text`. Omit `imageIcon` for no image; the builder fills it with a grey placeholder SVG.
- Call to action: `{"title":{"innerContent":{"desktop":{"value":"Title"}}},"content":{"innerContent":{"desktop":{"value":"<p>Body</p>"}}},"button":{"innerContent":{"desktop":{"value":{"text":"Label","linkUrl":"/"}}}}}`. It renders on the theme primary colour by default (`--gcid-primary-color`). Set `module.decoration.background` for another colour.
- Image: `{"image":{"innerContent":{"desktop":{"value":{"src":"<URL>","id":"<attachment ID as string>","alt":"<alt text>","titleText":"<title>","width":"<px>","height":"<px>"}}}}}`. Take `src`, `width` and `height` from `wp_get_attachment_image_src($id, 'full')`, and always write meaningful `alt` text.
- Images come from the media library (`get_posts(['post_type' => 'attachment', 'post_mime_type' => 'image'])`). If none fits, ask which image to use, or use the placeholder and say so.

## Create a draft page

```php
$id = wp_insert_post(['post_type' => 'page', 'post_status' => 'draft',
    'post_title' => $title, 'post_content' => wp_slash($content)], true);
if (is_wp_error($id)) return $id->get_error_message();
foreach (['_et_pb_use_builder' => 'on', '_et_pb_use_divi_5' => 'on',
          '_et_pb_page_layout' => 'et_full_width_page', '_et_pb_side_nav' => 'off',
          '_et_pb_post_hide_nav' => 'default'] as $k => $val) {
    update_post_meta($id, $k, $val);
}
ET_Core_PageResource::remove_static_resources($id, 'all');
return ['id' => $id, 'same' => get_post_field('post_content', $id) === $content,
        'builder_url' => add_query_arg('et_fb', '1', get_preview_post_link($id))];
```

- Always `wp_slash()` the content. `same` must be `true`.
- These five meta keys are enough; the builder adds its own settings meta on first save. Use `et_full_width_page` for full width; omit it to get the theme default (`et_right_sidebar`).
- Check first that no page with the same title exists, unless the user asked for a duplicate.

## Edit or copy an existing page

```php
$blocks = parse_blocks(get_post_field('post_content', $source_id));
// Modify $blocks[0]['innerBlocks'] (the placeholder's sections) ...
$content = serialize_blocks($blocks);
```

- `parse_blocks()` then `serialize_blocks()` round-trips builder content exactly, including components.
- When you add a child to a parsed block, also append `null` to that block's `innerContent`, or the child is not written. Builder-saved blocks keep newline strings in `innerContent` between children; leave them.
- Edit in place only when asked, and then call `remove_static_resources` afterwards.

### Clone a published page to a draft copy (verified)

Edit published content in a draft copy, never in the live page. Publish the copy only when the user asks. Replacing the live page with it means unpublishing or deleting the original, which is the user's job.

```php
$src = get_post($source_id);
$skip = ['_edit_lock', '_edit_last', '_wp_old_slug', '_wp_desired_post_slug', 'et_enqueued_post_fonts',
         '_divi_dynamic_assets_cached_modules', '_divi_dynamic_assets_cached_feature_used',
         '_divi_dynamic_assets_canvases_used', '_et_dynamic_cached_shortcodes',
         '_et_dynamic_cached_attributes', '_et_builder_module_features_cache'];
$id = wp_insert_post(['post_type' => $src->post_type, 'post_status' => 'draft',
    'post_title' => $src->post_title . ' (draft copy)', 'post_content' => wp_slash($src->post_content),
    'post_parent' => $src->post_parent, 'menu_order' => $src->menu_order], true);
if (is_wp_error($id)) return $id->get_error_message();
foreach (get_post_meta($source_id) as $k => $vals) {
    if (in_array($k, $skip, true) || str_starts_with($k, '_et_pb_ab_')) continue;
    foreach ($vals as $val) add_post_meta($id, $k, wp_slash(maybe_unserialize($val)));
}
update_post_meta($id, '_et_pb_post_settings_title', $src->post_title . ' (draft copy)');
```

Then edit the copy with parse, modify, serialise, call `remove_static_resources($id, 'all')`, and confirm the source's `post_content` hash is unchanged.

## Components (global modules)

A component instance on a page is `divi/global-layout` with `"globalModule":"<library post ID>"`, `"blockName":"divi/section"` (or the module type), `localAttrs`, `localAttrOverridePaths`, wrapping a full copy of the component's children, each with a `syncNodeId`. The source is an `et_pb_layout` post with term `scope: global`. Do not create component instances by hand. When copying a page, keep instance blocks unchanged. To add a component to a new page, ask the user to insert it in the builder.

## Silent failures (all verified on 5.15.1)

| Wrong | Result | Right |
|---|---|---|
| Attributes wrapped in `"attrs"` | Empty module | Attributes object directly |
| `divi/text` with `body` or `text` | Empty module | `content.innerContent` |
| `font.style: ["uppercase"]` | No CSS | `font.capitalization: "uppercase"` |
| `module.advanced.htmlAttributes.id.desktop.value` (per key) | Nothing | See CSS ID and class below |
| Button colours on `module.decoration` | Ignored | `button.decoration.background` / `border` / `font` |
| `phoneWide`, `tabletWide`, `widescreen`, `ultraWide` values | No CSS while those breakpoints are off | `tablet`, `phone` |
| Hand-written markup string | Escaping and nesting errors | `serialize_blocks()` |

## Common attribute paths (verified)

- Heading: `title.innerContent` (plain text); tag `title.decoration.font.font.<bp>.value.headingLevel` (default `h1`); size `...value.size`.
- Text: `content.innerContent` (HTML); font `content.decoration.bodyFont.body.font`.
- Button: `button.innerContent.<bp>.value` `{text, linkUrl, linkTarget}` (`linkTarget` `"on"` for a new tab, omit for same tab). Styling on `button.decoration`. Hover goes beside `value`: `"background":{"desktop":{"value":{"color":"#123"},"hover":{"color":"#456"}}}`. Alignment `module.advanced.alignment`.
- CSS ID and class: `module.decoration.attributes.desktop.value.attributes` = list of `{"id": wp_generate_uuid4(), "name": "id" or "class", "value": "...", "adminLabel": "CSS ID" or "CSS Class"}`.
- Breakpoints (defaults on): `desktop` (981px and up), `tablet` (768 to 980px), `phone` (767px and below).
- For any other module or path, read `wp-content/themes/Divi/includes/builder-5/visual-builder/packages/module-library/src/components/<module>/module.json` (attribute groups and `attrName` values) and `module-default-render-attributes.json` (defaults; do not write defaults). Or copy the shape from a page the builder saved.

## Verify and report

1. Read back `post_content` and compare with what you wrote.
2. Render check without a browser: set up the post and run `apply_filters('the_content', $post->post_content)`; every module should contain its text. Then `$S = implode(chr(92), ['ET', 'Builder', 'FrontEnd', 'Module', 'Style']); $S::render();` returns the module CSS.
3. Visual check. If you have a browser tool (Claude Code's built-in browser pane, or Claude in Chrome) and it is signed in to wp-admin, open `get_preview_post_link($id)` at desktop and phone width and look: layout, images, anything JavaScript-driven (sliders, tabs, sticky, video backgrounds). A quick script listing `.et_pb_module` elements with zero width or height finds blank modules. If no signed-in browser is available, say so and ask the user to look; never sign in yourself.
4. Report the page ID, title, status (draft), the builder URL, what you checked, and ask the user to open it in the Visual Builder and confirm the modules are editable.

Adapted in part from divilovewp/divi5-skill (MIT, copyright 2026 Shashank Gupta, divilove.com), verified and rewritten for Divi 5.15.1.
