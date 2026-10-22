Tested on Divi 5.15.1 (14 October 2026) and rechecked on 5.16.0 (21 October 2026) by rendering each shape and reading the emitted CSS. Load `divi5-core` first; this skill only adds styling paths. If `ET_BUILDER_VERSION` differs, verify on this site.

## Rules

- Style through module settings, never Custom CSS or a Code module.
- Every group below sits under the element it styles, usually `module.decoration.<group>`, with the breakpoint outermost: `{"desktop":{"value":{...}},"tablet":{"value":{...}},"phone":{"value":{...}}}`. Desktop cascades down; add `tablet` or `phone` only where they differ.
- Hover sits beside `value`, never inside it: `{"desktop":{"value":{"color":"#123456"},"hover":{"color":"#654321"}}}`.
- Do not write defaults (see each module's `module-default-render-attributes.json`). Global colours, variables and presets: use existing ones first; propose a new or changed one and write it only after the user agrees (below, and `divi5-presets`).

## Verified paths (`module.decoration.*` unless stated)

| Group | Value shape |
|---|---|
| `background` | `{"color":"#0f172a"}` |
| `background` gradient | `{"gradient":{"enabled":"on","type":"linear","direction":"90deg","stops":[{"position":0,"color":"#a00"},{"position":100,"color":"#00a"}]}}` |
| `spacing` | `{"padding":{"top":"40px","bottom":"40px","left":"20px","right":"20px"},"margin":{"top":"0px","bottom":"30px"}}` |
| `border` | `{"styles":{"all":{"width":"2px","color":"#ccc","style":"solid"}},"radius":{"topLeft":"12px","topRight":"12px","bottomLeft":"12px","bottomRight":"12px","sync":"on"}}` |
| `boxShadow` | `{"style":"preset1","horizontal":"0px","vertical":"8px","blur":"24px","spread":"0px","position":"outer","color":"rgba(0,0,0,0.15)"}`. `style` is required: without it nothing is emitted. |
| `sizing` | `{"maxWidth":"720px","minHeight":"400px"}` (columns also carry `flexType` here) |
| `filters` | `{"opacity":"90%","backdropBlur":"8px"}` (backdrop needs a translucent background to show) |
| `position` and `zIndex` | `position`: `{"mode":"relative","offset":{"vertical":"15px","horizontal":"30px"}}` emits `top: 15px; left: 30px`; `zIndex`: `"2"` |
| `sticky` | `{"position":"top","offset":{"top":"40px"}}` (also `bottom`, `topBottom`). Applied by Divi's script, so check it in a browser: it held in the column on scroll. |
| `background` video | `{"color":"#1e3a5f","video":{"mp4":"<URL>","width":"1280","height":"720"}}` (`webm` also accepted). Renders a muted, looping, autoplay video behind the section. Keep a fallback `color`. |

Sections and rows take the same groups. Padding on a section, `max-width` on a row and backgrounds on both were verified.

Image module border and radius go on `image.decoration.border`, which styles `.et_pb_image_wrap` and clips the picture. On `module.decoration.border` the radius lands on the outer wrapper with `overflow: visible`, so the image corners stay square (both checked in a browser on 5.16.0).

## Text

- Text module body: `content.decoration.bodyFont.body.font.<bp>.value`.
- Heading module: `title.decoration.font.font.<bp>.value`.
- Keys (verified): `size` (`"21px"`), `color`, `weight` (`"700"`), `lineHeight` (`"1.6em"`), `letterSpacing` (`"1px"`), `textAlign` (`"center"`; on the Text module Divi 5.16 ignores it, so use `module.advanced.text.text.<bp>.value.orientation` instead), `capitalization` (`"uppercase"`, `"lowercase"`, `"capitalize"`, `"smallCaps"`, `"allSmallCaps"`). Heading also takes `headingLevel`. `family` sets the font (`"Georgia"`, or a Google font such as `"Roboto Slab"` or `"Lato"`, which Divi enqueues itself). Button text font is `button.decoration.font.font`.
- `style:["uppercase"]` emits nothing. Use `capitalization`. `style` is for italic, underline and similar.

## Site colours and variables

Divi 5's Variable Manager holds global colours and variables of every type: `numbers`, `strings` (text), `images`, `links`, `colors`, `fonts`, `gradients`. List what the site defines, then reference it. Never invent an ID: an unknown ID renders as nothing.

```php
$G = implode(chr(92), ['ET', 'Builder', 'Packages', 'GlobalData', 'GlobalData']);
return ['colors' => $G::get_global_colors(), 'variables' => $G::get_global_variables()];
```

- Colour reference, where a colour string would go: `$variable({"type":"color","value":{"name":"gcid-primary-color","settings":{}}})$`, renders as `var(--gcid-primary-color)` (verified).
- Variable reference, where the value would go: `$variable({"type":"content","value":{"name":"gvid-<id>","settings":{}}})$`. Verified on 5.16.0 (T21, T25):
  - Number, in padding: renders `var(--gvid-<id>)`.
  - Font, in `font.<bp>.value.family`: renders `font-family: var(--gvid-<id>)`, and Divi loads the Google font. The site's built-in heading and body fonts use the bare name: `"name":"--et_global_heading_font"` or `"--et_global_body_font"`.
  - Text (strings), inside a Text `content.innerContent` value or a Heading `title.innerContent`: replaced by the variable's text.
  - Image, as `image.innerContent.<bp>.value.src`: replaced by the image URL.
  - Link, as `button.innerContent.<bp>.value.linkUrl`: replaced by the URL.
  - Gradient: `"type":"gradient"` in place of `"content"`, as the whole `module.decoration.background.<bp>.value.gradient` value: renders `background-image: var(--gvid-<id>)`.

### Create or change colours and variables (after the user agrees)

Use existing colours and variables first; keep the set lean, with no near-duplicates and nothing for a one-off value. You may propose a new or changed colour or variable yourself: give its type, label, value and where it would be used (a change affects every page using it). Write only after the user agrees.

```php
$G = implode(chr(92), ['ET', 'Builder', 'Packages', 'GlobalData', 'GlobalData']);
// Global colour (verified)
$c = $G::get_global_colors();
$cid = 'gcid-' . substr(md5(uniqid()), 0, 10);
$c[$cid] = ['color' => '#1f6f6b', 'label' => 'Teal', 'status' => 'active', 'folder' => '',
    'lastUpdated' => gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . '.000Z', 'usedInPosts' => []];
$G::set_global_colors($c);
// Variable of any type (verified for numbers, fonts, strings, images, links, gradients).
// A gradient's value is the string '$variable(' . wp_json_encode(['type' => 'gradient',
// 'value' => ['name' => 'gradient', 'settings' => <gradient object as in the table above>]]) . ')$'.
if ('sqlite' === (defined('DB_ENGINE') ? DB_ENGINE : '')) {
    add_filter('et_builder_5_global_variables_got_lock', fn() => '1');
}
$prev = array_map(fn($x) => (array) $x, (array) $G::get_global_variables());
$data = $prev;
$vid = 'gvid-' . wp_generate_uuid4();
$data['numbers'][$vid] = ['id' => $vid, 'label' => 'Section spacing', 'value' => '80px',
    'status' => 'active', 'order' => count($data['numbers']) + 1];
$err = $G::set_global_variables($data, $prev);   // null on success, WP_Error otherwise
```

- Pass every existing variable back: Divi rejects a save that drops an existing `gvid-` ID, so a variable cannot be deleted this way. Delete or archive a colour or variable only with the user's explicit approval. When proposing it, say what changes: every reference to it on every page stops resolving (an unknown ID renders as nothing), so list the pages that use it first. Divi's save cannot delete a variable; set its `status` to `archived` (the user can remove it in the Variable Manager).
- On WP Studio (SQLite) Divi's save lock always fails, because the database answers `GET_LOCK()` with `1=1`; the filter above fixes it for this request only. MySQL sites do not need it. The Studio site here also has an mu-plugin, `studio-divi-variables-lock.php`, that applies the filter everywhere, so the builder can save.
- Read back with `get_global_colors()` or `get_global_variables()`, then check a page using it in a browser.

## Responsive

Breakpoints on by default: `desktop` (981px and up), `tablet` (980px and below), `phone` (767px and below). Values for `phoneWide`, `tabletWide`, `widescreen` and `ultraWide` emit nothing until those breakpoints are switched on in Divi.

Adapted in part from divilovewp/divi5-skill (MIT, copyright 2026 Shashank Gupta, divilove.com), verified and rewritten for Divi 5.15.1.
