Tested on Divi 5.15.1, WordPress 7.1.3 (14 October 2026). If `ET_BUILDER_VERSION` differs, treat version-sensitive rules as "verify on this site".

## Ground rules

- Divi 5 is the only builder here. Do not ask which builder to use. Do not load `novamira-design` or `custom-theme-build` for Divi page work, and never create, save or activate a Novamira design unless asked.
- Use native Divi modules. No Code module, injected HTML, CSS or JavaScript for layout.
- New pages are drafts. Never publish, and never overwrite an existing page; copy it to a new draft instead, unless the user asks for an in-place edit.
- Do not write Divi global colours, variables, presets or components unless asked.
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

$column  = $b('divi/column', ['module' => ['advanced' => ['type' => $d('4_4')],
    'decoration' => ['sizing' => $d(['flexType' => '24_24'])]]], [$heading, $text, $button]);
$row     = $b('divi/row', ['module' => ['advanced' => ['columnStructure' => $d('4_4'),
    'flexColumnStructure' => $d('equal-columns_1')],
    'decoration' => ['layout' => $d(['flexWrap' => 'nowrap'])]]], [$column]);
$section = $b('divi/section', [], [$row]);

$content = serialize_blocks([['blockName' => 'divi/placeholder', 'attrs' => [],
    'innerBlocks' => [$section], 'innerHTML' => '', 'innerContent' => [null]]]);
```

### Rows and columns

| Columns | Row `columnStructure` | Row `flexColumnStructure` | Each column `type` | Each column `flexType` |
|---|---|---|---|---|
| 1 | `4_4` | `equal-columns_1` | `4_4` | `24_24` |
| 2 | `1_2,1_2` | `equal-columns_2` | `1_2` | `12_24` |

The number of `divi/column` children must match the structure. Widths are 24ths (`8_24` a third, `6_24` a quarter, `16_24` two thirds). To stack on phone, set the column `flexType` `phone` value to `24_24` and the row `module.decoration.layout` `phone` value to `{"flexWrap":"wrap"}`. Other structures: verify on this site (copy a row the builder saved).

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
- Copy to a new draft with the same meta keys plus the source's `_et_pb_page_layout`. Edit in place only when asked, and then call `remove_static_resources` afterwards.

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
3. Report the page ID, title, status (draft), the builder URL, and ask the user to open it in the Visual Builder and confirm the modules are editable.

Adapted in part from divilovewp/divi5-skill (MIT, copyright 2026 Shashank Gupta, divilove.com), verified and rewritten for Divi 5.15.1.
