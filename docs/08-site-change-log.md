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

User-created test pages (not Claude's; delete only if the user asks): ID 10 "T0 Baseline", ID 18 "T3 Component test", library item ID 22 "T3 Section Component".
