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
