# 05. Divi 5 authoring notes (unverified until tested)

Distilled from `divilovewp/divi5-skill` v0.6.9 (Divi 5.11.1, MIT, see NOTICE.md) and from `Projects\divi5-builder\divi5-builder-review.md` (measured on Divi 5.14.0 in WP Studio). Rewritten in condensed form. Every item is [Skill] unless marked. **Do not put any of this into a skill as fact until it has been run on the Studio site (5.15.1).** Tests are in docs/06.

## Page model

- Divi 5 stores a page as WordPress block markup in `post_content`: `<!-- wp:divi/<module> {json attributes} -->` with a self-closing form (` /-->`) for modules without children.
- Whole page wrapped in `<!-- wp:divi/placeholder --> ... <!-- /wp:divi/placeholder -->`. [Skill]. Not stated in the local docs, though a Divi changelog line mentions placeholder block validation on WordPress 7.1.
- Hierarchy: placeholder, section, row, column, then modules (or group, or a nested row). Never put a module directly in a section or row. Fullwidth sections (`module.advanced.type.desktop.value` of `fullwidth`) hold fullwidth modules such as `divi/fullwidth-header` directly.
- Child containers: accordion holds accordion-item, tabs holds tab, slider holds slide, pricing-tables holds pricing-table, counters holds counter, contact-form holds contact-field, icon-list holds icon-list-item, timeline holds timeline-item, group-carousel holds group, map holds map-pin.
- Every module block needs `"builderVersion"`. Use the installed version (`ET_BUILDER_VERSION`, `5.15.1` on the site). Older values still import through backward compatibility.

## Non-negotiable rules [Skill]

1. The JSON after the block name is the attributes object. **Never wrap it in `attrs`.** (`attrs` is only a key in preset definitions.) A wrapped module renders empty.
2. `divi/text` content key is `content`, not `body` or `text`.
3. `divi/heading` title is plain text. The tag comes from `headingLevel` in `title.decoration.font.font.<bp>.value`.
4. Button keys are `linkUrl` and `linkTarget` (`"off"` same tab, `"on"` new tab), not Divi 4's `url` and `newWindow`. Blurb title links use `url` and `target`.
5. A standalone button's visible styling (background, border, radius, padding, label font) lives on `button.decoration`, not `module.decoration`. Hover goes inside the breakpoint next to `value`: `desktop: { value: {...}, hover: {...} }`. Centre a button with `module.advanced.alignment` set per breakpoint.
6. `innerContent.<bp>.value` must never be an array (WordPress 500). `divi/audio` source is a plain string; `divi/video` source is an object with `src`.
7. Responsive values do not inherit upward. Always set `desktop`. Add `tablet` and `phone` only where behaviour differs.
8. Default breakpoints are `desktop`, `tablet`, `phone`. `phoneWide`, `tabletWide`, `widescreen`, `ultraWide` emit nothing unless the site enables them. Do not use `phoneWide` as "mobile". Skill's measured media queries on 5.10.1: tablet `max-width: 980px`, phone `max-width: 767px`, tabletWide `max-width: 1024px`, phoneWide `max-width: 860px` (when enabled), widescreen `min-width: 1280px` and `max-width: 1439px`, ultraWide `min-width: 1440px`. The local Divi docs describe the direction differently, so test T2 first.
9. Row layout is driven by `module.advanced.flexColumnStructure` (for example `equal-columns_3`) and `module.decoration.layout`, not by `display: flex`. The structure count must match the number of `divi/column` children. Column widths use `module.decoration.sizing.<bp>.value.flexType` as fractions of 24 (`24_24`, `12_24`, `8_24`, `6_24`, `16_24`). Set `phone` to `24_24` to stack.
10. CSS grid containers (`layout.display: "grid"`, `gridColumnCount`) do not stack on mobile. Set `gridColumnCount` per breakpoint.
11. Text transform comes only from `font.capitalization`. `style:["uppercase"]` stores and emits nothing. (Other `style` values such as italic and underline work.)
12. `htmlAttributes` has the breakpoint outermost: `module.advanced.htmlAttributes.desktop.value.{id,class}`. The per-key shape stores and renders nothing, which silently breaks in-page anchors.
13. Variable references need `"settings": {}`: `$variable({"type":"color","value":{"name":"gcid-...","settings":{}}})$`. Font references use `{"type":"content"}`. Only use `gcid-` and `gvid-` ids that exist on the site; invented ids render as nothing.
14. Do not include `groupPreset`. Unknown `modulePreset` ids fail silently.
15. Use real modules. `divi/code` is for genuine custom SVG art or third-party embeds only. Style through module decoration and Divi variables, not a shared stylesheet.
16. Native `backdrop-filter` exists since Divi 5.11 (`filters.<bp>.value.backdropBlur` and related keys). Do not use Custom CSS for it.

## Module gotchas [Skill]

- `divi/cta` always renders a default background (theme primary). Set an explicit background, `rgba(0,0,0,0)` for none.
- `divi/divider` draws its own line (on by default). Recolour via `divider.advanced.line`, do not add a border (double line).
- `divi/blurb` title value is an object `{ "text": "..." }`. Empty image `src` leaves a blank gap.
- `divi/testimonial` portrait and `divi/team-member` image use `url`, not `src`.
- `divi/fullwidth-header` buttons: label and URL both in `innerContent.desktop.value` as `{ text, linkUrl }`. Anything else crashes WordPress.
- `divi/menu` without `menuId` lists every published page and ignores theme menu locations.
- `divi/payment-button` `environment` defaults to `"sandbox"`.
- `divi/gallery` images cannot be passed in JSON (needs media IDs). The `boolean_checkbox` contact field type is silently skipped; use `checkbox` with one option.
- Transparent button background makes the label invisible; use a border.
- `divi/number-counter` shows a percent sign by default in the earlier review's tests (they could only switch it on, not off, with their compiler).
- Modules that need third-party plugins (Gravity Forms, Imagely, Woo, payment providers) save and render empty without them.

## Page meta and caches [Skill + earlier review]

- Pages need builder meta or they render with the theme default template and a sidebar. Skill: `_et_pb_use_builder = on` and `_et_pb_page_layout = et_full_width_page`. The earlier review's `set-builder` wrote `_et_pb_use_builder`, `_et_pb_use_divi_5`, `_et_pb_page_layout`, `_et_pb_side_nav`, `_et_pb_built_for_post_type`, `_et_builder_version`. Direct PHP (`update_post_meta`) avoids the REST protected-meta problem that those tools solve with a mu-plugin. Confirm the full set from a Visual Builder baseline page.
- `page-template-blank.php` suppresses Theme Builder headers and footers. For full width plus Theme Builder chrome, use the default template with the full-width layout meta.
- Divi caches each page's module list in post meta `_divi_dynamic_assets_cached_modules`. Adding a script-dependent module (slider, tooltip, table of contents, entrance animations, sticky) to a page whose cache predates it can leave its script unenqueued. Delete that meta and clear `wp-content/et-cache/` after programmatic edits. [Skill, tested 5.7.0 to 5.8.1]
- After bulk writes, page caches (LiteSpeed on the live hosting) must be purged or the changes are invisible. Not testable on Studio.

## Role and kses facts [earlier review, Divi 5.14.0, measured]

- Authors cannot create pages. A Contributor's write silently loses scripts, styles, iframes and `onclick` (kses). An Editor's write is stored intact. Relevant when writing from other users. Novamira runs as the connected administrator (user ID 1 here), so kses filtering is not expected, but `wp_insert_post` applies kses unless the user has `unfiltered_html`. Verify.
- Compiled defaults can override presets: pinning weight, alignment or icon on every button beats a preset's values (same specificity, later rule wins). Do not write default values into modules that use presets.

## Minimal skeleton to test (UNTESTED)

```
<!-- wp:divi/placeholder -->
<!-- wp:divi/section {"builderVersion":"5.15.1"} -->
<!-- wp:divi/row {"module":{"advanced":{"flexColumnStructure":{"desktop":{"value":"equal-columns_1"}}}},"builderVersion":"5.15.1"} -->
<!-- wp:divi/column {"module":{"decoration":{"sizing":{"desktop":{"value":{"flexType":"24_24"}}}}},"builderVersion":"5.15.1"} -->
<!-- wp:divi/heading {"title":{"innerContent":{"desktop":{"value":"Test heading"}},"decoration":{"font":{"font":{"desktop":{"value":{"headingLevel":"h2"}}}}}},"builderVersion":"5.15.1"} /-->
<!-- wp:divi/text {"content":{"innerContent":{"desktop":{"value":"<p>Body text.</p>"}}},"builderVersion":"5.15.1"} /-->
<!-- wp:divi/button {"button":{"innerContent":{"desktop":{"value":{"text":"Go","linkUrl":"/","linkTarget":"off"}}}},"builderVersion":"5.15.1"} /-->
<!-- /wp:divi/column -->
<!-- /wp:divi/row -->
<!-- /wp:divi/section -->
<!-- /wp:divi/placeholder -->
```

Assembled from the skill's examples. Whether minimal section attributes are valid, and whether `<p>` may appear raw or must be escaped in the stored JSON, are exactly what test T1 must establish. Compare against a page saved by the Visual Builder.

## What changed in Divi since the skill's target (local docs, 5.12 to 5.15.1)

From `divi-5-update-status.md` (read 5.14 to 5.15.1 only; 5.12 and 5.13 not read):
- 5.12: Divi AI Agent introduced (builds and edits layouts inside the Visual Builder; paid; official MCP "coming").
- 5.14: bug fixes for rows, accordion, blog image sizing, Gravity Forms buttons, presets on child themes.
- 5.15: components rework. Global modules are now components. New Slot module (`divi/global-slot`). Components import and export with layouts. This likely replaces the `divi/global-layout` approach.
- 5.15.1: fixes (Blurb layout flex alignment, Contact Form header injection, Gallery slider styles, Group Carousel equal height, Pricing Tables vertical gap, Tooltip scroll, Code module freeze, inline SVG strokes, others).
- Releases arrive roughly weekly. Preset behaviour changes often, so re-verify preset rules at each minor release.
