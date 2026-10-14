# 08. Site change log

Record every write to the Studio site `divi-5` (`http://localhost:8881`) here: date, what, how, how verified, how to undo. Reads do not need logging.

## State at handoff (14 October 2026)

No writes have been made by Claude. Only read-only calls were run:
- `discover-abilities`, `get-ability-info` (execute-php, save-design, skill-write)
- `execute-php` probes: versions, theme and Divi version, database type, block registry, breakpoint option, Divi-related postmeta keys, Novamira options, post types and plugin directory listing, search of plugin source for option names, feature manifest
- `skill-get` for `skill-creator` and `novamira-design`
- `list-design-library` (empty)

The site currently has: 0 user skills, no User Context, no active design, an empty sandbox folder, and none of the verification pages.

## Log

| Date | Change | Method | Verified by | Undo |
|---|---|---|---|---|
| 2026-10-14 | Draft page ID 9 "T6 Silent failures" (T6 and T2 variants); meta `_et_pb_use_builder=on`, `_et_pb_page_layout=et_full_width_page` | `execute-php`, `wp_insert_post` with `serialize_blocks()` | Read back `post_content`, identical to the written string | `wp_delete_post(9, true)` |
| 2026-10-14 | Draft pages ID 24 "T1a Hand-written", 25 "T1b Serialised", 26 "T4 Dynamic assets"; meta `_et_pb_use_builder on`, `_et_pb_use_divi_5 on`, `_et_pb_page_layout et_full_width_page`, `_et_pb_side_nav off`, `_et_pb_post_hide_nav default` | `execute-php`, `wp_insert_post` | Read back; 24 intact, 25 byte-identical to T0 | `wp_delete_post(<id>, true)` for each |
| 2026-10-14 | On draft 26: planted `_divi_dynamic_assets_cached_modules` and `_divi_dynamic_assets_cached_feature_used`, then ran `ET_Core_PageResource::remove_static_resources(26, 'all')` | `execute-php` | Both meta keys gone afterwards | None needed (removed by the test) |

| 2026-10-14 | Draft page ID 28 "T7 Skill recipe two columns" (divi5-core recipe test: two columns stacking on phone, then a parse, append, serialise edit) | `execute-php` | Read back identical; render shows all three texts; phone CSS `flex-wrap: wrap` at 767px | `wp_delete_post(28, true)` |
| 2026-10-14 | User Context text (docs/user-context-proposed.md, approved at gate c) saved to `novamira_instructions_content` and `nvp_instructions_content`. Feature `novamira/user-context` was already active. | `execute-php`, `\Novamira\Context\instructions_update_content()` | Both options equal the text; `novamira/agent-context` shows it first in `instructions` | `\Novamira\Context\instructions_update_content('')`, or switch User Context off on Novamira, Context |

| 2026-10-14 | User skill `divi5-core` (post ID 30, type `novamira_skill`) created, then content replaced twice with `skill-edit` to remove backslashes | `novamira/skill-write`, `novamira/skill-edit` | `skill-get`; stored body has 0 backslashes and the `chr(92)` fix; 8,797 bytes against 8,795 for `skills/divi5-core.md` (2-byte difference not yet located) | `novamira/skill-delete` slug `divi5-core` |

| 2026-10-14 | Sample-task drafts made by fresh agents through Novamira: ID 48 "S1b Landing Page" (round 2), ID 33 "S2 T0 Baseline with FAQ", 34 "S3 Image and text row", 35 "S1 Landing Page" | `execute-php` (agents following divi5-core) | Checked by Claude: drafts, five meta keys, every block stamped 5.15.1, escaping matches builder, parse and serialise round-trips; source ID 10 unchanged (1137 bytes) | `wp_delete_post(<id>, true)` for each |

| 2026-10-14 | Draft page ID 46 "T9 Module shapes" (three blurbs, one with media image 38; CTA with its own background; image module; four-column row) | `execute-php`, skill recipe | Read back identical; render shows every text, the image file and the CTA colour; phone wrap rule emitted | `wp_delete_post(46, true)` |
| 2026-10-14 | `divi5-core` round-2 content (layout templates, module-choice rule, blurb, CTA and image shapes) | `novamira/skill-edit` | Stored md5 `2489a461...` equals `skills/divi5-core.md` | Earlier text is in git history; `skill-edit` it back |

| 2026-10-14 | Draft page ID 49 "T10 Styling" (styling variants), later edited: first text module background set to a `gcid-primary-color` variable reference | `execute-php` | Read back identical; CSS checked per variant | `wp_delete_post(49, true)` |
| 2026-10-14 | Draft page ID 50 "T11 Modules" (tabs, toggle, two testimonials, contact form, slider, icon list, team member), later edited: team member image key `src` changed to `url` | `execute-php` | Read back identical; every text renders | `wp_delete_post(50, true)` |
| 2026-10-14 | User skills `divi5-styling` (post ID 53) and `divi5-modules` (post ID 54) | `novamira/skill-write` | Stored md5 equals `skills/divi5-styling.md` and `skills/divi5-modules.md`; 0 backslashes | `novamira/skill-delete` for each slug |

| 2026-10-14 | Draft page ID 55 "S5 Contact" (fresh-agent test of all three skills) | `execute-php` (agent following the skills) | Claude checked: draft; primary-colour variable, shadow and phone wrap emitted | `wp_delete_post(55, true)` |
| 2026-10-14 | Moved Claude's 13 test drafts to the bin: 9, 24, 25, 26, 28, 33, 34, 35, 46, 48, 49, 50, 55 (standing approval) | `execute-php`, `wp_trash_post()` | Each now has status `trash`; user pages 10, 18, 41 and library item 22 unchanged | Restore from Pages, Bin, or `wp_untrash_post(<id>)`; empty the bin to delete permanently |

| 2026-10-14 | Draft page ID 65 "T12 All modules" (63 modules, gradient section, sticky note, offset text, fonts, fullwidth section); later edited: audio set to media item 68 (WAV), new top section with an MP4 video background (MDN CC0 sample URL) | `execute-php` | Read back identical; render and browser checks (docs/09) | `wp_trash_post(65)` |
| 2026-10-14 | Draft page ID 66 "S5 Contact (draft copy)", cloned from published ID 55, hero heading edited in the copy | `execute-php`, clone recipe | Content copied byte for byte; ID 55 hash unchanged and still published | `wp_trash_post(66)` |
| 2026-10-14 | `divi5-core`, `divi5-styling`, `divi5-modules` updated (clone recipe, visual check, sticky, position, video background, fonts, T12 module shapes) | `novamira/skill-edit` | Stored md5 equals each file in `skills/` | Earlier text in git history |

| 2026-10-14 | Theme Builder header layout ID 81 and footer layout ID 83 (template 87, assigned only to page 55; built by the user, edit approved by the user): header gets a gradient, white text "TB header edited by Claude" and a button; footer becomes two columns, text and social follow | `execute-php`, parse, modify, serialise | Read back identical; template 87 meta and page 55 unchanged; browser: edits on page 55, Sample Page still has the default header | Restore the original content (both were one text module, "TB header text" and "TB footer text"; full originals in the session transcript and recreated by `wp_update_post` with that markup) |
| 2026-10-14 | User skill `divi5-theme-builder` (post ID 90) | `novamira/skill-write` | Stored md5 equals `skills/divi5-theme-builder.md` | `novamira/skill-delete` slug `divi5-theme-builder` |

| 2026-10-14 | Published movies ID 92 "Test movie two" and 93 "Test movie three" (user said to add more), with featured images 37 and 38 and all ACF fields set by `update_field()` | `execute-php` | `get_fields()` returned every value | `wp_trash_post(92)`, `wp_trash_post(93)` |

| 2026-10-14 | Draft page ID 94 "T14 Movies loop": looped group card and looped blurb over Movies (date field edited twice while testing) | `execute-php` | Render: 3 cards and 3 blurbs, per-movie titles, images, ACF text, WYSIWYG and links; no unresolved variables | `wp_trash_post(94)` |
| 2026-10-14 | Theme Builder: body layout ID 98 "Movies single body (Claude test)" and template ID 99 "All Movies" (`singular:post_type:movie:all`), added to `_et_template` of post 85 (before: 86, 87). Layout later edited: text module replaced by `divi/post-content` | `execute-php`, `et_theme_builder_store_template()` | Browser: movie pages show title, ACF fields, featured and ACF images, post content; Sample Page unchanged | `delete_post_meta(85, '_et_template', 99)`, then `wp_delete_post(99, true)` and `wp_delete_post(98, true)` |
| 2026-10-14 | `divi5-theme-builder` updated (create-template recipe); new user skill `divi5-dynamic` (post ID 103) | `novamira/skill-edit`, `novamira/skill-write` | Stored md5 equals the files in `skills/` | `skill-delete` slug `divi5-dynamic`; earlier text in git |

| 2026-10-14 | `group_date_field` meta on movies 92 and 93 converted from `20261120` / `20261225` to `2026-11-20 00:00:00` / `2026-12-25 00:00:00` after the user changed the ACF field to a Date Time Picker | `execute-php`, `update_post_meta` | Read back | Set the old `Ymd` values back |
| 2026-10-14 | Draft 94: date line switched to a formatted loop date. Layout 98: added a formatted "Release" line and a "More movies" section (group loop, `excludeCurrentPost`, 2 per page, by title) | `execute-php` | Loop dates formatted; each movie page lists the other two | Remove the added blocks with parse and serialise |
| 2026-10-14 | `divi5-dynamic` updated (date rules, `excludeCurrentPost`) | `novamira/skill-edit` | md5 equals `skills/divi5-dynamic.md` | Earlier text in git |

| 2026-10-14 | Draft page ID 109 "S6 Movies grid" (fresh Haiku agent following the skills) | `execute-php` (agent) | Claude checked in a browser: 3 cards side by side at 1280px, stacked at 447px | `wp_trash_post(109)` |
| 2026-10-14 | `divi5-dynamic` updated (looped-column grid) | `novamira/skill-edit` | md5 equals `skills/divi5-dynamic.md` | Earlier text in git |

| 2026-10-14 | Theme Builder: body layout ID 111 "Movies archive body (Claude test)" and template ID 112 "Movies archive" (`archive:post_type:movie`), added to `_et_template` of post 85 (before: 86, 87, 99). Static CSS cleared site-wide with `remove_static_resources('all', 'all')` (regenerates on next view) | `execute-php`, `et_theme_builder_store_template()` | `/movies/` page 1 shows two movies and a link to page 2; page 2 shows the third; browser screenshot | `delete_post_meta(85, '_et_template', 112)`, then delete posts 112 and 111 |
| 2026-10-14 | `divi5-dynamic` (pagination) and `divi5-theme-builder` (assignment strings, archives) updated | `novamira/skill-edit` | All five stored skills match `skills/` by md5 | Earlier text in git |
| 2026-10-14 | Wrap-up: moved Claude's drafts 65, 66, 94 and 109 to the bin | `wp_trash_post()` | Status `trash`; templates 87, 99, 112, layouts 81, 83, 98, 111, movies 91 to 93 and page 55 kept | Restore from Pages, Bin |

User-created test pages (not Claude's; delete only if the user asks): ID 10 "T0 Baseline", ID 18 "T3 Component test", library item ID 22 "T3 Section Component", ID 41 "T8 Reference", ID 55 "S5 Contact" (restored from the bin and published by the user; its slug is now `__trashed-16` because it was binned). Theme Builder record 85, templates 86 and 87, layouts 81 and 83 created by the user. CPT `movie` with ACF group "Movie custom fields" created by the user. Media items 36 to 40 and 68 (WAV) uploaded by the user. Media items 36 to 40 uploaded by the user.
