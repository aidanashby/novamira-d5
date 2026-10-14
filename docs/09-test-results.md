# 09. Test results

Site: Studio `divi-5`, Divi 5.15.1, WordPress 7.1.3. Connection: Claude Desktop's Novamira extension, shared into the Code tab (user chose to reuse it, 14 October 2026).

## Step 1. Connection [Run, 2026-10-14]

`execute-php` returned Divi `5.15.1`, WordPress `7.1.3`, environment `local`, user ID 1, home `http://localhost:8881`. Pass.

## T0. Baseline saved by the Visual Builder [Run, 2026-10-14]

The user built page ID 10 "T0 Baseline" (draft) in the Visual Builder: section, one-column row, heading (H2), text with an ampersand, quotes, a pound sign and bold, button linking to `/`.

Stored `post_content` (one block per line, `\n` between blocks; the placeholder and section open on one line):
- `divi/placeholder` with no attributes wraps everything.
- `divi/section {"builderVersion":"5.15.1"}`.
- `divi/row`: `module.advanced.columnStructure.desktop.value "4_4"`, `module.advanced.flexColumnStructure.desktop.value "equal-columns_1"`, `module.decoration.layout.desktop.value.flexWrap "nowrap"`.
- `divi/column`: `module.advanced.type.desktop.value "4_4"`, `module.decoration.sizing.desktop.value.flexType "24_24"`.
- `divi/heading`: `title.innerContent.desktop.value` plain text, `title.decoration.font.font.desktop.value.headingLevel "h2"`. Self-closing.
- `divi/text`: `content.innerContent.desktop.value` = `Fish &amp; chips cost "£5" <strong>today</strong>.` So the value is HTML (`&amp;` entity, `<strong>`, no `<p>` wrapper for a single line), and the JSON escapes `&`, `"`, `<`, `>` as `&`, `"`, `<`, `>`. `£` is stored raw (UTF-8). This is the escaping WordPress's `serialize_block_attributes()` applies.
- `divi/button`: `button.innerContent.desktop.value {text, linkUrl}`. No `linkTarget` stored when left at default.
- Every block carries `builderVersion` `5.15.1`. No default values are written (adminLabel, colours and so on stay out).

Post meta written by the builder: `_et_pb_use_builder on`, `_et_pb_use_divi_5 on`, `_et_pb_page_layout et_right_sidebar` (the theme default, not full width), `_et_pb_side_nav off`, `_et_pb_post_hide_nav default`, `_et_pb_show_page_creation off`, `_divi_off_canvas_data` (serialised, active canvas UUID and "Main Canvas"), plus empty or default `_et_pb_*` settings (`_et_pb_post_settings_title`, `_et_pb_ab_current_shortcode`, `_et_pb_enable_shortcode_tracking off`, `_et_pb_page_gutter_width_is_default 1`, and blank colour, CSS and excerpt keys). No `_et_builder_version` or `_et_pb_built_for_post_type` on a page. Page template blank (default).

## T1. Minimal page and round trip [Run, 2026-10-14]

| Draft | Method | Stored intact | Compared with T0 |
|---|---|---|---|
| ID 24 "T1a Hand-written" | Literal string, minimal docs/05 skeleton (row with `flexColumnStructure` only, column with `flexType` only), JSON escapes typed by hand, text wrapped in `<p>` | Yes | Same block names and value shapes. Lacks `columnStructure`, `advanced.type` and `flexWrap`. |
| ID 25 "T1b Serialised" | PHP arrays, T0's attribute set, `serialize_blocks()` | Yes | **Byte-identical to T0** after removing whitespace between blocks and swapping the label text. |

Both use meta `_et_pb_use_builder on`, `_et_pb_use_divi_5 on`, `_et_pb_page_layout et_full_width_page`, `_et_pb_side_nav off`, `_et_pb_post_hide_nav default`.

Conclusion so far: `serialize_blocks()` from PHP arrays reproduces the builder's storage exactly, so it is the method for the skill. Builder check (user, 2026-10-14): IDs 24, 25 and 9 opened in the Visual Builder with every module visible and editable. The text editor shows `<p>Fish &amp; chips cost "£5" <strong>today</strong>.</p>` for both 24 and 25 (the editor adds `<p>` to 25's unwrapped value) and the front end shows `&`. In 9 the only empty modules were the two broken variants (`attrs` wrapper, `body` key). **Pass.**

Round trip: saving 24 unchanged did not write (Divi skips a save with no changes). After the user changed only the heading text and saved, the content differed from the pre-save version only by the heading text and `\n` between blocks. The builder did not add `columnStructure`, `advanced.type` or `flexWrap`, and kept the escaping. It added its own settings meta (`_et_pb_post_settings_title`, `_divi_off_canvas_data`, `_et_pb_show_page_creation off` and blank `_et_pb_*` keys) and kept the five keys written by PHP. **Pass: a minimal page is stable through a builder save, and the five-key meta set is enough.** Pre-save hashes: 24 md5 `0db281de32da332a8d2794024805f48c` (1001 bytes), 25 `ef5493e1f2e260b007cf090a1d259e15` (1117), 9 `82c5d1b00a351f3f8cd5647f1e76fc14` (2719).

## T3. Components [Run, 2026-10-14]

The user saved a section as a component (in 5.15.1 "Save to Library" is under the section's hover-toolbar three-dot menu, not the right-click menu, which contradicts `components-in-divi-5.md`) and inserted it twice on page ID 18 "T3 Component test".

- The component is post ID 22, type `et_pb_layout`, status publish, terms `scope: global`, `layout_type: section`, `module_width: regular`; meta `_et_pb_built_for_post_type page`, `_et_pb_template_type section`, `_et_pb_use_divi_5 on`. Its content is a normal placeholder-wrapped section whose child blocks each carry a `syncNodeId` UUID. (Its content uses `\r\n` between blocks.)
- On the page, each instance is `<!-- wp:divi/global-layout {"globalModule":"22","blockName":"divi/section","builderVersion":"5.15.1","localAttrs":{"builderVersion":"5.15.1"},"localAttrOverridePaths":[]} -->` wrapping a full copy of the component's children (row, column, text), each with the same `syncNodeId` as the source and `localAttrOverridePaths: []`. The section block itself is replaced by `divi/global-layout`.
- `globalModule` is a string post ID. `divi/global-slot` was not used (it is for slots inside a component).

Verdict: `divi/global-layout` is current in 5.15.1. docs/04's "probably stale" was wrong. Skill rule: do not hand-write component instances; insert them in the builder, or copy an existing instance's markup exactly.

## T4. Dynamic-assets cache [Run + source, 2026-10-14]

- A loopback fetch from `execute-php` works (`wp_remote_get(home_url(...))`, HTTP 200). Logged-in draft previews were fetched with a short-lived user 1 auth cookie generated and used inside PHP.
- On a preview (`is_preview()`), Divi skips dynamic assets: it loads every module script and writes no `_divi_dynamic_assets_cached_modules` meta (draft ID 26). So drafts cannot show the stale-cache bug. It applies only to published pages seen by visitors.
- Source (`DynamicAssets.php`): when the meta exists it is used as the page's module list and never re-detected; nothing in the dynamic-assets code invalidates it on a programmatic content change. The skill's claim is consistent with 5.15.1 source.
- Divi's own invalidation is `ET_Core_PageResource::remove_static_resources($post_id, 'all')`, which calls `clear_post_meta_caches()` (deletes `et_enqueued_post_fonts`, `_et_dynamic_cached_shortcodes`, `_et_dynamic_cached_attributes`, `_et_builder_module_features_cache`, `_divi_dynamic_assets_cached_modules`, `_divi_dynamic_assets_cached_feature_used`, `_divi_dynamic_assets_canvases_used`) and removes static CSS. It returns early unless the current user can `edit_posts`, which holds under Novamira (user 1). Tested on draft 26: planted cache meta was removed.
- Rule for the skill: after any programmatic write, call `ET_Core_PageResource::remove_static_resources($id, 'all')`. A live published-page reproduction was not run (publishing is outside the standing approvals) and is not needed for the rule.

## Editing round trip [Run, 2026-10-14]

`serialize_blocks(parse_blocks($content)) === $content` for T0 (ID 10), T3 with components (ID 18) and T1a after a builder save (ID 24): exact, whitespace included. Parse, modify, serialise is safe for edits and copies.

## T7. divi5-core recipe [Run, 2026-10-14]

Draft ID 28, built by running the skill's recipe as written: two columns (`1_2,1_2`, `equal-columns_2`, columns `1_2` / `12_24` with phone `24_24`, row phone `flexWrap: wrap`), then a parse, append, serialise edit adding a text module (with `null` appended to the column's `innerContent`). Stored content identical; all three texts render; column classes `et_flex_column_24_24_phone`; CSS `@media only screen and (max-width: 767px) { .et_pb_row_0 {flex-wrap: wrap;} }`. Builder URL form `?page_id=28&preview=true&et_fb=1`. Not yet opened in the builder by the user.

## User Context [Run, 2026-10-14]

Written with `\Novamira\Context\instructions_update_content()` (writes both options; the admin screen only unslashes, no sanitising). "Enabled" is the `novamira/user-context` feature toggle, already on. `novamira/agent-context` shows the text first in `instructions`, ahead of Novamira's system context. The system context still says to load `novamira-design` and to ask which builder to use; the User Context and `divi5-core` override both.

## Skill storage strips backslashes [Run, 2026-10-14]

`skill-write` and `skill-edit` remove every backslash from `content`, including doubled ones (`&` became `u0026`, `ET\Builder\...` lost its separators, `"\n"` became a real newline). Skills must contain no backslashes: describe escapes in words and build namespaced class names with `implode(chr(92), [...])`. Check stored content with `substr_count($c, chr(92))`.

## Step 5. Sample tasks, round 1 [Run, 2026-10-14]

Each task ran in a fresh subagent limited to the Novamira tools (no local files, no web), starting from `agent-context`. All four loaded `divi5-core` unprompted, skipped `novamira-design` and `custom-theme-build`, and asked no questions they could default.

| Task | Draft | Valid | Draft + meta | Version | Native modules | Questions | Builder check | Score |
|---|---|---|---|---|---|---|---|---|
| S1 landing page | 35 | Yes | Yes | Yes | **No**: blurbs and CTA built from heading and text; three-column row values extrapolated | 0 | Pending | Partial |
| S2 copy plus FAQ | 33 | Yes | Yes (kept source layout) | Yes | Yes (`divi/accordion` from module.json) | 0 (would ask for real FAQ copy) | Pending | Pass |
| S3 image and text | 34 | Yes | Yes | Yes | Yes (`divi/image` from module.json; empty media library, used Divi `logo.png`) | 0 (would ask which image) | Pending | Pass |
| S4 explain page | none | n/a | n/a | n/a | Identified components correctly | 0 | n/a | Pass |

Fixes for round 2: a rule to use the module the user names (blurb, call to action, image and so on), verified blurb and CTA shapes, and three- and four-column row values. Divi's PHP has examples of one- and two-column rows only, so these come from a user-built reference page (T8).

## Step 5. Sample task 1, round 2 [Run, 2026-10-14]

Fresh agent, same prompt, after the round-2 skill update. Draft ID 48 "S1b Landing Page": hero (heading, text, button), `equal-columns_3` row of three `divi/blurb`, and `divi/cta`. Claude's check: draft, meta, every block stamped, three blurbs and the CTA render with all texts, `8_24` columns, phone wrap rule emitted. **Pass** (builder check pending). The agent skipped the skill's render step (it read back only); minor, noted for the next skill revision.

## T10. Styling [Run, 2026-10-14]

Draft ID 49, one text module per variant, CSS read with `Style::render()`. Emitted as expected: background colour; linear gradient (`linear-gradient(90deg, ...)`); padding (written with `!important`); margin; border style, colour and radius; box shadow with `style: preset1`; `maxWidth`, `minHeight`; body font `size`, `color`, `weight`, `lineHeight`, `letterSpacing`, `textAlign`; hover background (`:hover` rule); `filters.opacity` (`filter: opacity(91%)`) and `backdropBlur` (with `-webkit-` prefix); `position.mode relative` with `zIndex`; per-breakpoint padding (tablet in `max-width: 980px`, phone in `max-width: 767px`); section background and padding; row `maxWidth`. **Not emitted:** box shadow without `style` (skill's caveat confirmed).

Global colour reference `$variable({"type":"color","value":{"name":"gcid-primary-color","settings":{}}})$` as a background colour renders `background-color: var(--gcid-primary-color)`. `ET\Builder\Packages\GlobalData\GlobalData::get_global_colors()` lists five colours (primary, secondary, heading, body, link); `get_global_variables()` lists two font variables (`--et_global_heading_font`, `--et_global_body_font`, both Open Sans) and empty numbers, strings, images, links and colours. Global data is stored in `et_divi` option key `et_global_data`.

## T11. Modules [Run, 2026-10-14]

Draft ID 50. All texts render for tabs (first tab active), toggle (closed), testimonial (content, author, job title, company `{text, linkUrl}`), contact form (title, button, three fields, CAPTCHA added), slider (two slides, button), icon list and team member.

- Testimonial `portrait.innerContent`: `{src}` (the `module.json` key) and `{url}` (the skill's claim) both render.
- Team member `image.innerContent`: `{src}` renders nothing (`et_pb_team_member_no_image`); `{url}` renders. The skill's claim holds for this module.

## Step 6. divi5-styling and divi5-modules [Run, 2026-10-14]

Written from T8, T10 and T11 results only; unverified items are listed as such in each skill. The divi5-skill files read for this step (`DIVI5-STYLING.md`, `DIVI5-MODULES-INTERACTIVE.md`, `DIVI5-MODULES-MEDIA.md`) were treated as claims to test. Still unread: `DIVI5-DESIGN-PROCESS.md`, `DIVI5-PRESETS.md`, `DIVI5-PATTERNS.md`, `-DATA`, `-DYNAMIC`, `-WOOCOMMERCE`, `WORKFLOW.md` (presets and variables writes are outside the standing approvals; dynamic content is a later `divi5-dynamic` skill).

## Step 6 test. Styled contact page [Run, 2026-10-14]

Fresh agent, task needing styling and modules. Draft ID 55 "S5 Contact". It loaded `divi5-core`, `divi5-styling` and `divi5-modules` unprompted, listed global colours before referencing `gcid-primary-color`, used section background and padding, white centred heading and text, `equal-columns_2` with a testimonial and a contact form (three fields), radius and box shadow with `style`, and did not set a recipient email (asked instead). Claude checked: phone `flex-wrap: wrap` at 767px for both rows, phone column classes, primary-colour variable and shadow emitted. **Pass.** Step 7 (sandbox helper) skipped: no repeated friction a helper would fix.

## T12. All modules, clone, visual checks [Run, 2026-10-14]

**All modules.** Draft ID 65: every registered top-level module (63; Contact Form 7 not registered), in a section with a linear gradient, an `offset-columns_9` row with a sticky note in the narrow column, an offset text module, three font families, a fullwidth section and (added later) a video-background section. Render: no PHP output, every module wrapper present, all marker texts present except three explained below. In the built-in browser (signed in by the user), 62 of 65 top-level modules have a visible box. Zero-size: dropdown (hidden until triggered), lottie (no animation URL), fullwidth map (no Google Maps API key). Empty or different by design: signup (no email provider), payment button (no provider), post filter ("Select a Loop"), login (shows "Log out" to an admin).

- Fonts: `family` Georgia (text), Roboto Slab (heading), Lato (button `button.decoration.font.font`) all computed and loaded; Divi enqueued Roboto Slab from Google Fonts.
- Sticky `module.decoration.sticky` `{"position":"top","offset":{"top":"40px"}}`: stayed pinned on scroll (seen in browser). No CSS: applied by script.
- Position `{"mode":"relative","offset":{"vertical":"15px","horizontal":"30px"}}`: `top: 15px; left: 30px`.
- Video background `background.desktop.value.video {mp4, width, height}`: `<video autoplay loop playsinline muted>` and playing in the browser.
- Audio with the media library WAV: rendered. Gallery `{galleryIds}`: images shown. Select, checkbox and radio contact fields: rendered.
- Phone (375px): the two-column row stacks (both columns full width, second below), no horizontal overflow.

**Clone.** Published ID 55 to draft ID 66: content byte-identical, builder meta and page template copied (cache, lock and A/B keys skipped; `_wp_desired_post_slug` should also be skipped and is now in the recipe), heading edited in the copy and visible in the browser preview, ID 55 unchanged and still published.

**Visual checks.** The built-in browser pane, signed in once by the user, gives drafts at desktop and phone widths, screenshots and read-only DOM checks. Added to `divi5-core` as step 3 of "Verify and report".

## T13. Theme Builder layouts [Run, 2026-10-14]

The user built a template in Divi, Theme Builder, assigned to page 55 only, with a custom header and footer. Storage: `et_theme_builder` post 85 lists templates in repeated meta `_et_template` (86 default, 87); template 87 has `_et_use_on` `singular:post_type:page:id:55`, `_et_header_layout_id` 81, `_et_footer_layout_id` 83, `_et_body_layout_id` 0 (default), `_enabled` flags, `_et_default` 0, `_et_autogenerated_title` 1. Layouts 81 (`et_header_layout`) and 83 (`et_footer_layout`) are published posts holding normal Divi 5 block markup and builder meta.

Claude edited both layouts with parse, modify, serialise (header: section gradient, text colour and size, an added button; footer: replaced with an `equal-columns_2` row of text and social follow). Browser (built-in pane): page 55 shows both edits in `.et-l--header` and `.et-l--footer`, the gradient computes, the footer columns stack at 375px, and the default Divi header is replaced. The Sample Page keeps the default header. **Pass.** New skill `divi5-theme-builder`. Not tested: body layouts, creating templates or changing assignments by PHP (left to the Theme Builder screen).

## T14. Loops, ACF and a CPT Theme Builder template [Run, 2026-10-14]

Movies (`movie`, ACF group "Movie custom fields": `text_field`, `image_field`, `wysiwyg_field`, group `group` with `date_field` date picker and `url_field`). Three published movies (91 by the user, 92 and 93 by Claude).

- **Loop.** `module.advanced.loop` `{enable, loopId, queryType: post_types, subTypes: [{value: movie, label: Movies}], postPerPage, orderBy, order}` on a `divi/group` card and on a `divi/blurb` (draft 94). Each repeated three times with the right title, featured image (thumbnail size honoured), ACF text, WYSIWYG HTML and permalink. Names: `loop_post_title`, `loop_post_featured_image`, `loop_post_link`, `loop_post_meta_key_manual_custom_field` with `select_loop_meta_key` `loop_post_meta_key_<field>`.
- **Date limit.** The group subfield date (`group_date_field`, stored `Ymd`) with any `date_format` renders 1970: Divi gets no `acf_type` for it, and `format_date` reads `20261017` as a timestamp. Adding `acf_type` by hand is ignored. `loop_acf_<group>|||<sub>` options exist only for ACF repeaters (Pro). Shown unformatted instead.
- **CPT single template.** Body layout 98 plus template 99 (`singular:post_type:movie:all`) created with `et_theme_builder_store_template()` and linked in post 85. Current-post names: `post_title`, `post_featured_image`, `post_meta_key` with `select_meta_key` `custom_meta_<field>`; `post_content` is not a dynamic name, use `divi/post-content`. Browser: movie 91 shows title, text field, WYSIWYG, website URL, post content, its featured image and its different ACF image; the Sample Page is unchanged.
- New skill `divi5-dynamic`; `divi5-theme-builder` gained the create-template recipe.

## T15. Dates and loop filters [Run, 2026-10-14]

- Divi's `ModuleUtils::format_date()` treats digits-only values as Unix timestamps and parses other strings with `strtotime()`. ACF Date Picker stores `Ymd`, so it shows 1970.
- After the user switched `date_field` to a Date Time Picker (stores `Y-m-d H:i:s`), the loop shows "16 Oct, 2026", "20 Nov, 2026", "25 Dec, 2026" with `date_format` `j M, Y`.
- Outside a loop, `post_meta_key` reads ACF's Return Format (`d/m/Y g:i a`), so the custom display format is ignored and the raw text shows. Fix proposed to the user: Return Format `Y-m-d H:i:s`.
- `excludeCurrentPost` `on` with `orderBy` `title`: each single movie page's "More movies" loop lists the other two movies in title order.

## Fresh-agent test of divi5-dynamic and divi5-theme-builder (Haiku) [Run, 2026-10-14]

A Haiku subagent limited to Novamira tools (about 98,000 tokens) loaded `divi5-core`, `divi5-dynamic` and `divi5-theme-builder` unprompted. Task 1, "S6 Movies grid" (draft 109): it looped a `divi/column` (`8_24` desktop, `24_24` phone) inside a row with `flexWrap: wrap`, with cards of image, heading, ACF text and link; no unresolved variables. Claude's browser check: three across at 1280px, stacked at 447px. Task 2 (read only): it correctly reported template 99, its assignment, and body layout 98's contents. **Pass.** The looped-column grid was added to `divi5-dynamic`.

## T16. Movies archive template and loop pagination [Run, 2026-10-14]

The user turned on Has archive for Movies (`/movies/`, HTTP 200). `et_theme_builder_get_template_settings_options()` lists the valid assignment IDs; for Movies: `singular:post_type:movie:all`, `singular:post_type:movie:id:`, `archive:post_type:movie`. Template 112 with body layout 111: heading, a looped-column grid (`loopId` `movie_archive`, `postPerPage` `2`, `12_24` columns) and a `divi/post-nav` with `module.advanced.targetLoop` `movie_archive`. Divi paginates loops through a query parameter named after the `loopId`. Page 1 showed "Movie title" and "Test movie two" with formatted dates and a link to `?movie_archive=2`; page 2 showed "Test movie three" with a link to `?movie_archive=1`. Browser: two cards side by side and a "Next" link. **Pass.**

The ACF Return Format was still `d/m/Y g:i a` at this point (the user had changed the Display Format), so single-page dates still showed unformatted.

## T8. Reference page from the builder [Run, 2026-10-14]

The user built page ID 41 "T8 Reference" and confirmed drafts 28, 33 and 34 look correct in the Visual Builder (so the accordion shape from `module.json`, the image shape and the parse, append, serialise edit all pass in the builder).

- **Columns added one by one** (the user's method) are stored as a one-column row (`columnStructure 4_4`, `equal-columns_1`) holding several `24_24` columns with `flexWrap: nowrap`. They render side by side because they shrink, but never stack.
- **Divi's layout templates** (`visual-builder/build/constant-library.js`) define `equal-columns_N`, `offset-columns_N`, `multi-row-grids_N`, `multi-column-grids_N`. Each sets the row to `nowrap` desktop and `wrap` phone, and most set column `flexType` per desktop width with `24_24` on phone. Examples: `equal-columns_3` = `8_24` x3; `equal-columns_4` = `6_24` x4 (no phone value in the template); fifths and sevenths use `1_5`, `1_7`.
- `columnStructure` and `advanced.type` are legacy: the builder left them at `4_4` on rows with three and four columns.
- Blurb title is `{"text": ...}`. With no image chosen, the builder stores a grey placeholder SVG data URI in `imageIcon`.
- CTA renders `background-color: var(--gcid-primary-color)` by default.
- Image stores `{src, id, alt, titleText, width, height}` plus a `module.decoration.attributes` title attribute (with both a top-level `attributes` list and the `desktop` form).

## T9. Round-2 shapes [Run, 2026-10-14]

Draft ID 46 built with the round-2 recipe: `equal-columns_3` with three blurbs (one with media image 38), a CTA with `module.decoration.background` `#1e3a5f`, an image module from `wp_get_attachment_image_src(38, 'full')`, and `equal-columns_4`. Stored identical; all texts and the image file render; CTA colour emitted; phone `flex-wrap: wrap` emitted. Builder check pending.

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
