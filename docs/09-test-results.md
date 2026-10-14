# 09. Test results

Site: Studio `divi-5`, Divi 5.15.1, WordPress 7.1.3. Connection: Claude Desktop's Novamira extension, shared into the Code tab (user chose to reuse it, 14 October 2026).

## Step 1. Connection [Run, 2026-10-14]

`execute-php` returned Divi `5.15.1`, WordPress `7.1.3`, environment `local`, user ID 1, home `http://localhost:8881`. Pass.

## T5. Schema cross-check (offline, read-only) [Run, 2026-10-14]

Source: the site's own theme, `<Studio sites folder>\divi-5\wp-content\themes\Divi` (style.css `Version: 5.15.1`), so no 5.14.0 versus 5.15.1 gap. Module metadata is in `includes/builder-5/visual-builder/packages/module-library/src/components/<module>/` (116 `module.json` files, plus `module-default-render-attributes.json` and `module-default-printed-style-attributes.json`). PHP is in `includes/builder-5/server/`.

| Claim (docs/05) | Result |
|---|---|
| Heading text at `title.innerContent`, tag at `title.decoration.font.font.<bp>.value.headingLevel` | Pass. Default `headingLevel` is `h1`. |
| Text content key is `content` (`content.innerContent`), fonts `content.decoration.bodyFont` / `headingFont` | Pass. |
| Button: text and link in `button.innerContent`; visible styling on `button.decoration` (background, border, boxShadow, button, font, sizing, spacing); alignment at `module.advanced.alignment` | Pass. `module.decoration` of a button has no background or border group. Default `button.decoration.button.desktop.value.icon.enable` is `on`. |
| CTA: `title`, `content`, `button` | Pass. |
| Blurb: `title` (no innerContent group item in metadata), `content`, `imageIcon.innerContent` | Pass for paths. Title value shape (`{text}`) needs T6 or a builder save. |
| Column width `module.decoration.sizing.<bp>.value.flexType`, default `24_24` | Pass. |
| Row layout via `module.advanced.flexColumnStructure` | Pass, from Divi's own PHP. Divi's `WooCommerceHooks.php` writes rows with both `module.advanced.columnStructure` (`4_4`, `1_2,1_2`) and `flexColumnStructure` (`equal-columns_1`, `equal-columns_2`) plus `module.decoration.layout.<bp>.value.flexWrap`, and columns with `module.advanced.type` (`4_4`, `1_2`) plus `flexType`. Compare with T0. |
| Placeholder wrapper and `builderVersion` on every block | Pass, from Divi's own PHP: the same file builds pages as `<!-- wp:divi/placeholder -->...` with `"builderVersion":"__DIVI_BUILDER_VERSION__"` replaced at runtime. Divi itself writes hand-built block markup. |
| `htmlAttributes` with breakpoint outermost | **Superseded.** `server/Migration/AttributeMigration.php` (release `5.0.0-public-alpha.23`) moves `module.advanced.htmlAttributes.desktop.value.{id,class}` into `module.decoration.attributes.desktop.value.attributes[]` as `{id: uuid, name: "id" or "class", value, adminLabel: "CSS ID" or "CSS Class"}`. Whether the old path still renders in content stamped 5.15.1 goes to T6. |
| Capitalisation only from `font.capitalization` | Pass. `FontCapitalizationMigration.php` (5.8.1) moves `uppercase`, `lowercase`, `capitalize` (to `smallCaps`), `smallCaps`, `allSmallCaps` out of `font.style`, only for modules stamped below 5.8.1. |
| `divi/global-layout` is stale | **Not supported by the source.** `GlobalModuleSyncNodeMigration.php` (5.15.0) still looks for `wp:divi/global-layout` instance blocks with a `globalModule` post ID and adds `syncNodeId` values. The block is not in the block registry, but content still uses the name. T3 settles it. |

### Migration rule found

Each migration in `server/Migration/` runs only on modules whose `builderVersion` is below its release version (`StringUtility::version_compare(... '<')`). Stamping `5.15.1` therefore skips every migration, so markup must already be in the current shape. Legacy shapes stamped with a current version are not repaired.

Migration release versions on 5.15.1: alpha.17.1 (global colour), alpha.18.2 (flexbox), alpha.23 (attributes), alpha.24 (fullwidth portfolio), beta.1 (nested module), beta.7 (dynamic content post ID), beta.7.1 (empty array corruption), 5.1.1 (composable options), 5.3 (focus fields), 5.4.2 (image group), 5.8.1 (font capitalisation), 5.9.0 (grid offset rules), 5.15.0 (global module sync node).

## T2. Breakpoints, part 1: Divi's API [Run, 2026-10-14]

`ET\Builder\Framework\Breakpoint\Breakpoint` on the site: enabled `desktop`, `tablet`, `phone`; base `desktop`. Style settings: phone `max-width: 767px`; tablet `min-width: 768px` and `max-width: 980px`; desktop `min-width: 981px`. Disabled by default: phoneWide (max 860px), tabletWide (max 1024px), widescreen (min 1280px), ultraWide (min 1440px). Phone and tablet are max-width (desktop-first), widescreen and ultraWide min-width. This matches the skill's measured table and contradicts the local docs' wording. Part 2 (emitted CSS on a rendered draft) below.

## T6 and T2 part 2. Silent failures and emitted CSS [Run, 2026-10-14]

Draft ID 9 "T6 Silent failures", built from PHP arrays with `serialize_blocks()` and stamped `ET_BUILDER_VERSION`. Rendered inside `execute-php` with `apply_filters('the_content', ...)` and `ET\Builder\FrontEnd\Module\Style::render()` (style key `post`), so no browser login is needed. Stored `post_content` was byte-identical to the written string (no kses change for user 1).

| Variant | Rendered result | Verdict |
|---|---|---|
| Heading `font.capitalization: "uppercase"` | `text-transform: uppercase` | Skill confirmed |
| Heading `font.style: ["uppercase"]` | No CSS | Skill confirmed (silent failure) |
| `module.advanced.htmlAttributes.desktop.value.{id,class}` | `id="t6-legacy-id"`, class added | Renders. Legacy path. |
| `module.advanced.htmlAttributes.id.desktop.value` (per key) | Nothing | Skill confirmed (silent failure) |
| `module.decoration.attributes.desktop.value.attributes[]` (`name` id or class) | `id="t6-new-id"`, class added | Renders. Current path (T5). Prefer it. |
| Text with JSON wrapped in `attrs` | Empty module wrapper, no text | Skill confirmed (silent failure) |
| Text with `body` instead of `content` | Empty module wrapper | Skill confirmed (silent failure) |
| Button `button.decoration.background.desktop.{value,hover}` | `background-color` plus `:hover` rule | Skill confirmed |
| Heading sizes desktop 40, tablet 30, phone 20, phoneWide 25 | 40px base; `@media only screen and (max-width: 980px)` 30px; `(max-width: 767px)` 20px; phoneWide nothing | Skill confirmed; local docs' wording on direction is wrong |

Other observations: a column with `flexType: 24_24` on desktop only gets `et_flex_column_24_24_tablet` and `_phone` classes, so desktop values cascade down to smaller breakpoints in the class output. The button default `icon.enable: on` emitted an `:after` font-size rule.

## Divi 5.12 to 5.13.1 release notes [Docs, 2026-10-14]

Read from `divi-5-update-status.md`. No change to the core module schema. Relevant: 5.12 AI Agents; 5.12 module presets now emit interactions, wrapper attributes and CSS classes on the front end; 5.12.1 new sliders default to swipe controls (converted sites keep dots); 5.13 and 5.13.1 Show Labels on Contact Form, Email Optin and Comments; 5.13 hidden modules no longer load front-end assets.

## Novamira sandbox and crash recovery [Web, 2026-10-14]

- A must-use loader `require_once`s every `*.php` in `wp-content/novamira-sandbox/` on every request. `.disabled` files are skipped. The sandbox is not a security boundary.
- A fatal during sandbox load writes `wp-content/novamira-sandbox/.crashed`; the next request skips all sandbox files (safe mode). Abilities keep working. Exit by deleting `.crashed` (Novamira, Sandbox, Exit Safe Mode, or `delete-file`). Nothing is rolled back.
- `execute-php` errors do not trigger crash recovery.
- Consequence for the helper: define functions only, guarded by `function_exists`, with no side effects at load time.
