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

The ACF Return Format was still `d/m/Y g:i a` at this point (the user had changed the Display Format), so single-page dates still showed unformatted. After the user set Return Format to `Y-m-d H:i:s`, the single pages showed "16 October 2026, 2:17 pm", "20 November 2026, 12:00 am" and "25 December 2026, 12:00 am" (custom format `j F Y, g:i a`) and the archive kept "16 Oct, 2026". The `divi5-dynamic` date rules are now verified in both contexts.

## T17. Loop filters by custom field [Run, 2026-10-14]

`module.advanced.loop.desktop.value.metaQuery` is a list of `{key, value, compare, type}` (also accepts `metaKey`, `metaValue`), built into a `meta_query` by `LoopUtils::build_meta_query()`. Draft 116, three column loops over Movies:

| Filter | Result |
|---|---|
| `group_date_field` `>=` `2026-11-01 00:00:00`, type `DATETIME` | Test movie two, Test movie three (same as a plain `WP_Query`) |
| `text_field` `LIKE` `Second` | Test movie two |
| `group_date_field` `BETWEEN` `2026-11-01 00:00:00,2026-12-01 00:00:00`, type `DATETIME` | Test movie two |

DATETIME comparison works on the SQLite database. Limits from the source: several items are always joined with `OR` (Divi sets `relation`), so two different conditions cannot both be required; `value` is fixed text, so there is no "today" token and date windows do not move on by themselves. The second limit is the gap the user's Date Conditions for Divi plugin targets.

## T18. Loop filters by taxonomy term [Run, 2026-10-14]

The user added taxonomy `genre` (flat, public) with Action (8), Romcom (7) and Sci-fi (6); Claude assigned them to the three movies. `includePostWithSpecificTerms` / `excludePostWithSpecificTerms` are lists of `{categoryId: <taxonomy>, selectedOptions: [{value: <term ID>, label}]}`, built into a `tax_query` (`field` `term_id`, `operator` `IN`) separate from the `meta_query`. Draft 124:

| Filter | Result |
|---|---|
| Include Sci-fi | Test movie three, Test movie two |
| Include Action | Movie title |
| Exclude Sci-fi | Movie title |
| Include Sci-fi and `metaQuery` date `>=` 2026-12-01 | Test movie three (both must match) |
| Include Action or Romcom | Movie title, Test movie two |

`loop_post_terms` needs `taxonomy_type` `genre` (the default is `category`, which printed nothing); with `links` `on` each genre links to its archive.

Variables: the user reports that variables cannot be saved in the builder on 5.15.1. The local changelog (to 5.15.1) mentions no such bug; not investigated, at the user's request.

## T19. Presets [Run, 2026-10-14]

Under the user's one-day permission. The user made a module preset (Button) and an option-group preset (Heading, Title Text) in the builder and applied them on T0 Baseline. Storage: option `builder_global_presets_d5`, read with `GlobalPreset::get_data()`: `module[<block>]` and `group[<group name>]`, each `{default, items}`; items carry `attrs` and `styleAttrs` (identical for style settings), `version`, `created`, `updated` in milliseconds; group items add `groupName` `divi/font`, `moduleName`, `groupId` `designTitleText`. The builder also created empty default presets. On a module: `"modulePreset":["<id>"]` (a list) and `"groupPreset":{"designTitleText":{"presetId":["<id>"],"groupName":"divi/font"}}`. This corrects two divi5-skill claims (docs/04 and docs/05): `modulePreset` is a list, and `groupPreset` is current and valid.

Claude created two presets with `prepare_data()` and `save_data()` (Divi's own save path; `save_data()` clears static CSS site-wide) and applied them on draft 130. Browser: the blurb preset gave background, padding and radius; the heading group preset gave Georgia, navy, 36px; the user's 40px preset applied; a module's own colour beat the preset's (title red over navy, button green over the preset's red). Button colours were confirmed by screenshot: a script reading the computed colour reported transparent, probably because of Divi's button background transition. The skill's create-and-edit recipe, run as written, created and edited a third preset with defaults and other presets unchanged. **Pass.** New skill `divi5-presets` keeps preset writes gated.

Fresh Haiku agent (about 92,000 tokens): "S7 Preset demo" (draft 132). It loaded `divi5-core` and `divi5-presets`, applied the existing module and group presets by ID, gave the second button a local green, and created or changed no preset. Its PHP render check could not see preset styles (preset CSS is printed separately from `Style::render()`); Claude's screenshot confirmed the serif navy heading, the red preset button and the green one. **Pass.**

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

## T20. Design showcase gate (fresh Haiku agents) [Run, 2026-10-15]

Skill `divi5-design-system` plus the User Context routing sentence. Three fresh Haiku agents limited to Novamira tools, run in parallel, each given only a fictional brief. Pages counted before (max post ID 137) and after.

| Test | Brief | Result |
|---|---|---|
| A, no direction | New site, five pages, no brand guide (Riverside Tool Library) | Built only draft 140 "Design showcase: Riverside Tool Library", proposed palette and fonts as plain values, wrote no globals, stopped and asked. None of the five pages built. **Pass** |
| B, references | Approved brand guide with colours, fonts, button style; Home page only (Oakfield Community Larder) | No showcase; built draft 139 following the guide. **Pass** |
| C, skip | "Skip the design showcase", Home page (Hillcrest Repair Café) | No showcase; built draft 138. **Pass** |

Only pages 138 to 140 were created. Claude's browser check of 140: no blank modules, no horizontal overflow, Nunito and Nunito Sans loaded, every row stacks to one column at 375px. Faults found: buttons rendered in Open Sans (the button font was not set), cards had no surface, no image row; the agent also said the global colour list was empty when it holds Divi's five defaults. The skill now requires the button font (`button.decoration.font.font`), card surfaces and an image and text row. None of the agents could check their page in a browser (Novamira tools only), as the skill expects them to report.

## T21. Writing global colours and variables [Run, 2026-10-15]

- Root cause of "variables will not save": WP Studio's SQLite driver (1.8.0) answers `SELECT GET_LOCK(...)` with `1=1`. Divi's `set_global_variables()` treats anything but `1` as locked and returns "Global variables are currently being saved by another request", so every variable save fails on this site, from the builder or PHP, whether or not the Variable Manager is open. MySQL sites (Krystal) should not be affected [Inferred].
- With Divi's own filter `et_builder_5_global_variables_got_lock` returning `1` for the request, a number variable saved and read back. A global colour saved with `set_global_colors()` (no lock involved).
- Draft 146: a heading with padding from the variable and colour from the colour. Browser: padding 24px, colour rgb(31,111,107), both `:root` custom properties defined. **Pass.**
- Divi rejects a variables save that drops an existing `gvid-` ID (`global_variable_ids_missing`), so variables cannot be deleted through it; the test variable was archived instead. The test colour was removed.
- Only the number type and `gcid` colours were rendered. The other variable types use the same save but their references are untested.

## T22. Page production from supplied content (fresh Haiku agent) [Run, 2026-10-15]

Skill `divi5-page-production` (post 153). Brief: Volunteer page for the fictional Oakfield Community Larder, with an approved brand guide and content containing a planted contradiction (shop floor shift Tuesday 10am to 2pm against opening hours Tuesday 10am to 1pm), two gaps (no form link, no surname) and three identical "Apply for this role" buttons.

- Draft 154 "Volunteer with us": one `h1`, five `h2`, three `h3`; all content present. The contradiction and both gaps appear as visible `[placeholder: ...]` text and are listed in the report; button links are `#` and reported. No facts invented. **Pass.**
- Proposed the brand colours and Lora as globals without writing them. Presets and variables unchanged (hash before and after); the stored colour option is unchanged.
- Claude's browser check: no blank modules or overflow at 1280px, roles row three across; every row one column at 375px.
- Misses: the agent did not propose a preset for the three repeated buttons (skill now says to); its first insert lost all but one section because it built the placeholder's `innerContent` by hand, which it found and fixed on its own draft.
- Finding for audits: `get_global_colors()` stamps Divi's customizer colours with the current time on every read, so compare the stored option `et_global_data`, not the getter's output.

## T23. Upgrade check, Divi 5.15.1 to 5.16.0 and Novamira 1.12.9 to 1.12.11 [Run, 2026-10-21]

Scope set by the changelogs (Divi 5.16.0 dated 16 October, Novamira 1.12.10 and 1.12.11). Novamira's changes cover OAuth, Gutenberg editing, Read Mode and Troubleshoot, none of which the skills use. Divi's include a Text module alignment fix, a Menu collapsible mobile option, Tabs and Gallery fixes, and Visual Builder multi-select.

- Connection and context: `novamira/agent-context` lists all eight user skills and the User Context text. The option `novamira_instructions_enabled` no longer exists in the database; 1.12.11 lists it as a legacy option, and the text is still injected.
- Stored skills: all eight `post_content` and excerpt md5s match `skills/` and `skills/manifest.json`.
- Module registry: the set of `module.json` names is unchanged (183 in both versions).
- T1: parse then serialise of T0 (ID 10) is byte-identical. **Pass.**
- T6 and T10 (draft 203 "T23 Upgrade check 5.16.0", 18 variants): every 5.15.1 result repeats (capitalization, `style` silent failure, CSS ID and class, `attrs` and `body` empty modules, button hover, heading breakpoints, background, gradient, spacing with breakpoints, border, shadow with and without `style`, filters, position and z-index, global colour, hover), with one exception. **Changed:** the Text module ignores body-font `textAlign` (5.16 strips it at render time, `TextModule::remove_body_font_text_align`) and emits `text-align: start`. `module.advanced.text.text.desktop.value.orientation` `"center"` emits `text-align: center`. Heading `textAlign` still renders. `divi5-styling` updated.
- T12, partial (draft 205 "T23b All modules 5.16.0", 68 top-level modules with no attributes): stored intact, no PHP output, every wrapper present except Tabs, Payment Button and Shortcode, which render nothing when empty. Not checked in a browser or the Visual Builder; the changelog has no storage change for these.
- T12b, browser (draft 239, a copy of T12's filled-in draft 65 with its 5.15.1 stamps): at 1280px and 375px, 65 top-level modules, the same three zero-size as on 5.15.1 (dropdown, lottie, fullwidth map), Tabs shows both tabs with content, no PHP notices, no horizontal overflow, and the two-column row stacks at 375px. Payment Button renders nothing without a provider, as on 5.15.1. Not covered: Instagram Feed (needs an account), the post-content modules (Theme Builder only) and the Shortcode module (Divi's wrapper for converted Divi 4 shortcodes). **Pass.** Draft binned.
- Both drafts moved to the bin.

## T24. Design skill routing on Novamira 1.12.11 (fresh Haiku agent) [Run, 2026-10-21]

Novamira 1.12.11's system context says "Before any visual work ... load the `novamira-design` skill". Dry run, no writes: "Our site looks dated. Please redesign the homepage with a fresh, modern look."

- Loaded, in order: `divi5-core`, `divi5-design-system`, then `novamira-design`, citing the system context line. It noticed that `divi5-core` says not to load `novamira-design`, and loaded it anyway "for taste guidance only".
- Builder choice and approval gate held: it did not ask which builder, called `get-active-design` (none active), and planned a draft design showcase first, stopping for the brief and approval.
- **Partial fail:** the skill rule alone does not outweigh the system context instruction. The User Context needs to say it.
- Side notes: it also loaded an unrelated Claude Code skill first (agent habit, not site routing), and noticed the skills say "tested on 5.15.1" against 5.16.0 and planned to verify on the site, as `divi5-core` tells it to.
- Fix (user-approved, gate c): User Context sentence "Do not load the built-in novamira-design skill; divi5-design-system does that job on this site." Present in `agent-context`.
- Retest, fresh Haiku agent, same prompt: loaded `divi5-design-system`, `site-design` (not found, so no direction set), `divi5-core`, `divi5-styling`; excluded `novamira-design`, quoting the new sentence. Stopped before the showcase draft to ask for the brief. **Pass.**
- Found: `divi5-design-system` line 54 names a `divi5-audit` skill that does not exist on the site or in `skills/`.

## T25. Variable references of every type [Run, 2026-10-21]

Draft 210 "T25 Variable references", Divi 5.16.0. Five test variables created with user approval (font Lato, text, image, link, gradient), then archived (Divi's save cannot delete). Reference shapes taken from the Divi source (`DynamicData.php`, `DynamicContentGlobalVariableOptions.php`, `GlobalData::resolve_global_gradient_variable`), then rendered and checked in the browser pane.

| Type | Reference and place | Result |
|---|---|---|
| Built-in fonts | `content`, name `--et_global_heading_font` / `--et_global_body_font`, in `font.desktop.value.family` | `font-family: var(--et_global_...)`, Open Sans in the browser |
| Font | `content`, `gvid-`, in `family` | `font-family: var(--gvid-...)`; `:root` defines `'Lato'`; Divi enqueues Lato from Google Fonts |
| Text | `content`, `gvid-`, inside Text `content.innerContent` and as Heading `title.innerContent` | Replaced by the text in both; no token left in the HTML |
| Image | `content`, `gvid-`, as `image.innerContent.desktop.value.src` | `<img src>` is the image URL, with srcset and `wp-image-36` |
| Link | `content`, `gvid-`, as `button...linkUrl` | `href="/contact/"` |
| Gradient | `gradient`, `gvid-`, as `module.decoration.background.desktop.value.gradient`; variable value is a `$variable({"type":"gradient","value":{"name":"gradient","settings":{...}}})$` string | `background-image: var(--gvid-...)`, computed as the 90deg gradient |

**Pass** for every type. `divi5-styling` updated. Not checked: opening draft 210 in the Visual Builder (binned).

## T26. Menu collapsible mobile submenus (new in Divi 5.16) [Run, 2026-10-21]

Draft 213 "T26 Collapsible mobile menu": one `divi/menu` with `menuMobile.advanced.collapsible` `on` and `parentLinksActive` `off` (paths from the Menu `module.json` and `MenuModule.php` line 83). Stored intact; the wrapper carries `et_pb_menu--collapsible-mobile` and `et_pb_menu--collapsible-parent-links-inactive`. **Pass (render only).** Not checked: expand and collapse in a browser, because the site has no nav menu with child items and creating one is outside the standing approvals. Draft binned; `divi5-modules` updated.

**Browser check (T26b, user approved a test menu).** Nav menu 9 "T26 Test menu": Home; About with Our team and History; Services with Advice. Draft 226 "T26b Collapsible menu browser check" held one `divi/menu` with `menu.advanced.menuId` `"9"`, `collapsible` `on`, `parentLinksActive` `off`. In the browser pane at 375px, signed in: the hamburger opened the mobile list with both submenus closed (`display: none`) and a plus toggle (`button.mobile-toggle`, `aria-expanded="false"`) on each parent. Tapping the About label opened its submenu (`aria-expanded="true"`, minus icon) and the URL stayed on the draft; tapping the minus closed it; tapping the Services plus opened Advice. **Pass.** Not checked: whether opening one submenu closes another, and `parentLinksActive` `on`. Draft binned; menu 9 kept for later reruns.

**Leftovers (T26c).** Draft 236 "T26c Menu leftovers" with two Menu modules on menu 9, collapsible on, `parentLinksActive` `off` and `on`. At 375px: in the first, About stayed open when Services was opened, so submenus open independently. In the second, the plus opened About's submenu without leaving the page, and tapping the About name followed the link to `/about-test/`. **Pass.** Draft binned.

## T27. Visual Builder saves variables on WP Studio [Run, 2026-10-21]

With the mu-plugin `studio-divi-variables-lock.php` active, the user added a Number variable "T26 builder save" = 12px in the Variable Manager (opened from T0, page not saved). Read back: stored as 12px, active; the other eight variables unchanged. **Pass.** The SQLite `GET_LOCK()` blocker (T21) is resolved for the builder too.

## T28. Outstanding visual checks [Run, 2026-10-21]

- Live drafts 138 "Home", 139 "Oakfield Community Larder Home", 140 "Design showcase: Riverside Tool Library" and 154 "Volunteer with us", checked in the browser pane at 1280px and 375px: no zero-size modules, no horizontal overflow, and at 375px every multi-column row stacks. **Pass.** Drafts 46, 48 and 130 (earlier pending checks) are in the bin, so those checks lapse.
- Button preset colour (T19 doubt): T0 Baseline's button with the user's preset `7ehrfhnk99` computes `rgb(158, 6, 6)` (the preset's `#9e0606`) and white text once Divi's 0.2s button transition has finished. The earlier "transparent" reading was taken mid-transition. **Resolved.**

## T29. Moving date windows through Date Conditions for Divi [Run, 2026-10-21]

Divi's `metaQuery` takes fixed values (T17), so "from today" loops rely on the user's plugin Date Conditions for Divi 0.1.4, installed on Studio but inactive. Read from its code: rules live only in block attributes on the module with the loop (`dcfdDateRules.innerContent.desktop.value` with `rule1Field`, `rule1Operator` `after`/`before`/`equals`, optional rule 2, both must match; `dcfdEmptyState` holds a published Divi Library item ID shown when nothing matches). It adds a `meta_query` on `divi_loop_data_before_execution`, accepts ACF Date Picker (Ymd, end of day) and Date Time Picker (`Y-m-d H:i:s`) fields, names group subfields `<group>_<field>`, lets posts with an empty field through, and needs Divi 5.8.1, ACF and page caching off. `divi5-dynamic` now describes it, marked untested, and tells agents never to activate it. **Run (user approved activating the plugin):** draft 217 "T29 Date Conditions test" held three Text modules, each looping Movies by title with `dcfdDateRules` on `group_date_field` (a Date Time Picker). Rendered through `the_content` at 2026-10-21 14:19: `after` gave "Movie title" (16 Oct, past); `before` gave "Test movie three" (25 Dec) and "Test movie two" (20 Nov), both future; `after` plus `before` as rule 1 and rule 2 gave nothing, so both rules must match. No `$variable(` text left. This matches the code (`after` is now later than the field). `divi5-dynamic` corrected; draft 217 binned. **Not run:** a post with an empty field (all three movies have dates) and `dcfdEmptyState`.

**Edge cases (T29b, user approved).** Published movies 229 "T29 Movie no date field" (no meta row) and 230 "T29 Movie empty date" (stored `''`), published Divi Library item 231 "T29 Empty state", and draft 232 with loops `before`, `after` and `equals` (the last with `dcfdEmptyState` `"231"`). At 14:36: `before` gave both undated movies plus the two future ones; `after` gave both undated movies plus "Movie title"; `equals` gave only the two undated movies, and the empty-state item did not show. With 229 and 230 set to draft, `before` and `after` gave the dated movies only, and `equals` gave nothing and rendered the Library item. **Pass:** undated posts pass every rule, the empty state shows only when nothing matches, and Divi's loop skips drafts. 229 to 232 binned.

## T30. Fresh-site install test [Run, 2026-10-21]

A separate Claude Code session in a fresh clone (HEAD `1f0da77`) ran `novamira-setup` on a second Studio site, `divi-install-test` (http://localhost:8882: WordPress 7.1.3, PHP 8.4.26, Divi 5.16.0, Novamira 1.12.11, SQLite), then built a design showcase, a Walks loop and a Theme Builder template for a custom post type. Writes are in docs/08, second section.

**Setup: pass, with snags.** All eight skills uploaded first time and md5-matched; the User Context and lock fix went in and verified; the smoke test passed and the user edited it in the Visual Builder. Snags, each fixed in the repo:
- Divi installed but not active gave `ET_BUILDER_VERSION` null, which the skill treated as no Divi. Setup now checks `wp_get_themes()` and offers to activate it.
- The clone's working copy of `tools/studio-divi-variables-lock.php` was CRLF (`core.autocrlf` true; `.gitattributes` covered only `skills/`), so its md5 differed from the LF copy written to the site. `.gitattributes` is now `* text=auto eol=lf`.
- Setup did not report the Novamira version; it now does.
- The session had to edit `CLAUDE.md` (written for the original site) and append to docs/08. `CLAUDE.md` is now generic, the maintainer's rules are in a git-ignored `CLAUDE.local.md`, and user site writes go to a git-ignored `site-logs/` folder.

**Verified findings (new skill rules):**
- Image module border and radius belong on `image.decoration.border` (selector `.et_pb_image_wrap`, from the image `module.json`). On `module.decoration.border` the browser showed a 14px radius on the outer wrapper with `overflow: visible` and square image corners; after the move the wrap measured 14px with `overflow: hidden`. `divi5-styling`.
- A site where the Theme Builder was never used has no `et_theme_builder` post. `et_theme_builder_get_theme_builder_post_id(true, true)` created post 36 (it returns the newest published one, excluding library sets, and creates one only when none exists: read in `theme-builder.php`). `et_theme_builder_insert_layout()` created body layout 37 with its `layout_type` term and builder enabled. Template 38 "All Walks" rendered on three walk pages at 800px and 375px and not on `/sample-page/`. `divi5-theme-builder`.
- An ACF Link field (stored as an array) used as a button `linkUrl` through `post_meta_key` rendered an empty `href`. `divi5-dynamic`.
- Accordion item titles default to `h5`; `title.decoration.font.font.<bp>.value.headingLevel` `"h3"` rendered `h3` (rendered HTML). The toggle's `module.json` has the same `headingLevel` field (not run). `divi5-modules`.
- Loop images with `thumbnail_size` `et-pb-post-main-image` all measured 195x122 in the browser, where mixed aspect ratios had given uneven cards. `divi5-dynamic`.
- `loop_post_featured_image` rendered images with no alt text when the media items had none. `divi5-dynamic` now has agents check and ask.
- Removing a child from a parsed column by reference left `serialize_blocks()` warning "Undefined array key" although child and `null` counts matched afterwards. `divi5-core` now has a removal recipe.

**Seen but not yet verified:**
- Background image with a gradient overlay: `module.decoration.background.<bp>.value` `{"color", "image": {"url", "size": "cover", "position": "center"}, "gradient": {"enabled": "on", "overlaysImage": "on", ...}}` (from Divi's background source files). The rendered CSS layered `linear-gradient(...)` over `url(...)`, but the draft was not seen in a browser. Not added to the skills.
- The post content module in a Theme Builder body shows the theme's default font. Its `module.json` lists `module.decoration.bodyFont` and `headingFont`; the value shape is not yet run. Not added.

**Not adopted:** a chunked or helper-based skill upload (the eight skills, about 57 KB, uploaded first time and the md5 check catches a bad copy), and `novamira/create-admin-access-link` for signing in the browser (it creates a working admin login; agents ask the user to sign in instead).
