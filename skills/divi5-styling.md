Tested on Divi 5.15.1 (14 October 2026) by rendering each shape and reading the emitted CSS. Load `divi5-core` first; this skill only adds styling paths. If `ET_BUILDER_VERSION` differs, verify on this site.

## Rules

- Style through module settings, never Custom CSS or a Code module.
- Every group below sits under the element it styles, usually `module.decoration.<group>`, with the breakpoint outermost: `{"desktop":{"value":{...}},"tablet":{"value":{...}},"phone":{"value":{...}}}`. Desktop cascades down; add `tablet` or `phone` only where they differ.
- Hover sits beside `value`, never inside it: `{"desktop":{"value":{"color":"#123456"},"hover":{"color":"#654321"}}}`.
- Do not write defaults (see each module's `module-default-render-attributes.json`). Do not write global colours, variables or presets.

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
| `position` and `zIndex` | `position`: `{"mode":"relative"}`; `zIndex`: `"2"` |

Sections and rows take the same groups. Padding on a section, `max-width` on a row and backgrounds on both were verified.

## Text

- Text module body: `content.decoration.bodyFont.body.font.<bp>.value`.
- Heading module: `title.decoration.font.font.<bp>.value`.
- Keys (verified): `size` (`"21px"`), `color`, `weight` (`"700"`), `lineHeight` (`"1.6em"`), `letterSpacing` (`"1px"`), `textAlign` (`"center"`), `capitalization` (`"uppercase"`, `"lowercase"`, `"capitalize"`, `"smallCaps"`, `"allSmallCaps"`). Heading also takes `headingLevel`.
- `style:["uppercase"]` emits nothing. Use `capitalization`. `style` is for italic, underline and similar.

## Site colours and fonts (read only)

List what the site defines, then reference it. Never invent an ID: an unknown ID renders as nothing.

```php
$G = implode(chr(92), ['ET', 'Builder', 'Packages', 'GlobalData', 'GlobalData']);
return ['colors' => $G::get_global_colors(), 'variables' => $G::get_global_variables()];
```

A colour reference goes where a colour string would: `$variable({"type":"color","value":{"name":"gcid-primary-color","settings":{}}})$`. It renders as `var(--gcid-primary-color)` (verified). Font and number variable references are not yet verified here; use plain values.

## Responsive

Breakpoints on by default: `desktop` (981px and up), `tablet` (980px and below), `phone` (767px and below). Values for `phoneWide`, `tabletWide`, `widescreen` and `ultraWide` emit nothing until those breakpoints are switched on in Divi.

Adapted in part from divilovewp/divi5-skill (MIT, copyright 2026 Shashank Gupta, divilove.com), verified and rewritten for Divi 5.15.1.
