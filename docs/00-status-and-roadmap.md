# 00. Status and roadmap

Planning, progress tracking and next steps for the project. The README describes the project; this file says where it stands. Test evidence is in docs/09, every site write in docs/08.

## Status (21 October 2026)

The Studio site runs Divi 5.16.0 and Novamira 1.12.11. The upgrade check (T23) found one change, Text module alignment, and `divi5-styling` now covers it. Novamira 1.12.11 also tells agents to load its `novamira-design` skill for visual work; a User Context sentence now overrides that (T24).

The original plan (docs/06, "Recommended order") is complete, and the scope was then widened to Theme Builder, dynamic content and custom post types. Both gates the user owned are decided: no Novamira Pro (gate e), and Divi globals stay the source of truth for design tokens (gate d, option 1). See docs/10.

### On the Studio site now

| Item | Where | Notes |
|---|---|---|
| User Context | option `novamira_instructions_content` | Text in docs/user-context-proposed.md (revised 14 October: publish when asked, never delete or unpublish) |
| Skill `divi5-core` | post 30 | Structure, `serialize_blocks()` recipe, meta, rows and columns, module choice, clone, components, silent failures, verification including browser checks |
| Skill `divi5-styling` | post 53 | Decoration paths, fonts, hover, sticky, position, video background, responsive, global colour references |
| Skill `divi5-modules` | post 54 | Shapes for every registered module, contact form fields, fullwidth sections |
| Skill `divi5-theme-builder` | post 90 | Theme Builder storage, editing layouts, creating templates for a post type and its archive |
| Skill `divi5-dynamic` | post 103 | Dynamic content, ACF fields, Loop Builder, pagination, filters by custom field and term, dates, custom post types |
| Skill `divi5-presets` | post 131 | Read and apply module and option-group presets; propose new or changed ones, write after the user agrees |
| Skill `divi5-design-system` | post 136 | New-site and redesign workflow: design showcase draft and approval gate (T20) |
| Skill `divi5-page-production` | post 153 | Build a draft page from supplied content, with gaps and sources reported (T22) |
| Theme Builder template "All Movies" | template 99, body layout 98 | Single movie pages (`singular:post_type:movie:all`), with a "More movies" loop |
| Theme Builder template "Movies archive" | template 112, body layout 111 | `/movies/` (`archive:post_type:movie`), paginated grid |
| Theme Builder template for S5 | template 87, layouts 81 and 83 | Built by the user, header and footer edited by Claude |
| Test movies | posts 92, 93 | Published, with ACF values; post 91 is the user's |

The skill sources in `skills/` match the stored skills byte for byte (md5 checked after every upload, last on 21 October 2026).

### What was tested (on Divi 5.15.1; T1, T6, T10 and part of T12 rerun on 5.16.0 in T23)

| Area | Result |
|---|---|
| Storage method | `serialize_blocks()` from PHP arrays is byte-identical to the Visual Builder; parse, modify, serialise round-trips exactly (T0, T1) |
| Builder compatibility | Every PHP-written draft opened, edited and saved in the Visual Builder |
| Silent-failure rules from divi5-skill | Confirmed or corrected (T5, T6) |
| All registered modules | 62 of 63 visible in a browser; the rest need outside setup (T12) |
| Styling | Backgrounds, gradients, video backgrounds, spacing, borders, shadows, fonts, hover, sticky, position, breakpoints (T10, T12) |
| Clone a published page | Byte-identical draft copy, original untouched (T12) |
| Theme Builder | Edit layouts; create single and archive templates for a post type (T13, T14, T16) |
| Loops and ACF | Group, blurb and column loops; ACF text, WYSIWYG, image, URL, date; `excludeCurrentPost`; pagination; filters by custom field and by taxonomy term (T14 to T18) |
| Presets | Builder and PHP-made module and group presets, applied and checked in a browser; local settings override presets (T19) |
| Fresh agents | Eight fresh agents with only the skills completed every task after one fix round (six on Opus, two on Haiku) |

## History

1. Chat session (Claude Desktop): Novamira installed and connected, research written up (docs/01 to docs/07).
2. Claude Code session: tests T0 to T7, User Context, `divi5-core`, sample tasks in two rounds, `divi5-styling` and `divi5-modules`, decision memo and gates.
3. Same session, widened scope: all-modules page, cloning, browser checks, Theme Builder, loops, ACF, Movies single and archive templates, `divi5-theme-builder` and `divi5-dynamic`.
4. 15 October: presets, design system and page production skills, global variables (T19 to T22), setup skill.
5. 21 October: upgrade check for Divi 5.16.0 and Novamira 1.12.11 (T23).

## Open items

- Done (23:05): the user set the date field's Return Format to `Y-m-d H:i:s`; single movie pages now show "16 October 2026, 2:17 pm" and the archive "16 Oct, 2026".
- The theme's sidebar and footer widgets ("Archives", "Categories") show under Theme Builder bodies on movie pages. Cosmetic; a global footer in the Theme Builder would replace them.

## Roadmap (not started)

| Next step | Why | Needs |
|---|---|---|
| Moving date windows ("from today") | Divi's `metaQuery` takes fixed values only (T17) | Out of scope here (the user's Date Conditions for Divi plugin) |
| Divi upgrade check | Divi ships weekly; skills say "tested on 5.15.1" | Read the Divi and Novamira changelogs after each update and rerun only the tests the changes touch (T23 is the pattern) |
| Real sites | Use the skills on Foodbank and Bristol Charities staging builds | Staging sites with Novamira, never live |

## Setup skill: version rules (decided 15 October 2026)

Planned Claude Code skill to install the Novamira skills and User Context on a new WordPress + Novamira site. Each check changes what it does:

- Divi missing or below 5: stop; the skills are Divi 5 only.
- Divi 5 below 5.7.0: warn, then run the T1 round-trip test before page work. 5.7.0 is the earliest version with every feature the skills use (gradient variables were added in 5.7.0; loop pagination targeting appears by 5.3.0; Loop Builder, ACF dynamic content, group presets, variables, flex rows, Theme Builder, sticky, position and video backgrounds were all in the 5.0.0 public betas). Source: Divi changelog, read 15 October 2026. Most lines there are fixes, so these are latest-possible dates. Components were excluded. No warning from 5.7.0 up.
- Novamira lacking `execute-php`, `skill-write`, `skill-edit` or User Context: stop and name what is missing.
- SQLite database (WP Studio): add the variables lock mu-plugin.
- WordPress version: not checked; nothing depends on it.

## Learnings worth keeping

- Novamira's skill storage strips every backslash from skill text, even doubled ones. Skills avoid backslashes and build class names with `implode(chr(92), [...])`.
- The Write tool turned `&` into `&` in a local file, so the local and stored skills differed by two bytes. Hash-check after every upload.
- Save to Library is under a section's three-dot hover menu in 5.15.1, not the right-click menu the local docs describe. The local docs also describe the breakpoint direction wrongly.
- Divi skips a save in the builder when nothing changed.
- Binning a page renames its slug (`__trashed-N`), and restoring it does not rename it back.
- Logged-in previews skip Divi's dynamic-assets cache, so cache bugs only show on published pages.
- The browser pane only shows drafts once the user signs in. `.et-l--body` wraps ordinary pages too, so check for content, not that class.

## Resuming in a new session

1. Read `CLAUDE.md`, this file and the README.
2. Check the connection: `novamira/execute-php` returning `ET_BUILDER_VERSION`. If the Novamira tools are missing from a Claude Code session, switch the "Novamira, Divi 5" extension off and on in the session's + menu, Connectors, after Studio is running; the tools arrive on the next turn.
3. Compare the stored skills with `skills/` by md5 before editing either.
4. Pick from Open items or the Roadmap. Follow the standing approvals and gates in CLAUDE.md, and log every site write in docs/08.
