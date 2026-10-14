# 00. Status and roadmap

Planning, progress tracking and next steps for the project. The README describes the project; this file says where it stands. Test evidence is in docs/09, every site write in docs/08.

## Status (14 October 2026, 23:10 BST)

The original plan (docs/06, "Recommended order") is complete, and the scope was then widened to Theme Builder, dynamic content and custom post types. Both gates the user owned are decided: no Novamira Pro (gate e), and Divi globals stay the source of truth for design tokens (gate d, option 1). See docs/10.

### On the Studio site now

| Item | Where | Notes |
|---|---|---|
| User Context | option `novamira_instructions_content` | Text in docs/user-context-proposed.md (approved at gate c) |
| Skill `divi5-core` | post 30 | Structure, `serialize_blocks()` recipe, meta, rows and columns, module choice, clone, components, silent failures, verification including browser checks |
| Skill `divi5-styling` | post 53 | Decoration paths, fonts, hover, sticky, position, video background, responsive, global colour references |
| Skill `divi5-modules` | post 54 | Shapes for every registered module, contact form fields, fullwidth sections |
| Skill `divi5-theme-builder` | post 90 | Theme Builder storage, editing layouts, creating templates for a post type and its archive |
| Skill `divi5-dynamic` | post 103 | Dynamic content, ACF fields, Loop Builder, pagination, dates, custom post types |
| Theme Builder template "All Movies" | template 99, body layout 98 | Single movie pages (`singular:post_type:movie:all`), with a "More movies" loop |
| Theme Builder template "Movies archive" | template 112, body layout 111 | `/movies/` (`archive:post_type:movie`), paginated grid |
| Theme Builder template for S5 | template 87, layouts 81 and 83 | Built by the user, header and footer edited by Claude |
| Test movies | posts 92, 93 | Published, with ACF values; post 91 is the user's |

The skill sources in `skills/` match the stored skills byte for byte (md5 checked after every upload).

### What was tested (all on Divi 5.15.1)

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
| Fresh agents | Seven fresh agents with only the skills completed every task after one fix round (six on Opus, one on Haiku) |

## History

1. Chat session (Claude Desktop): Novamira installed and connected, research written up (docs/01 to docs/07).
2. Claude Code session: tests T0 to T7, User Context, `divi5-core`, sample tasks in two rounds, `divi5-styling` and `divi5-modules`, decision memo and gates.
3. Same session, widened scope: all-modules page, cloning, browser checks, Theme Builder, loops, ACF, Movies single and archive templates, `divi5-theme-builder` and `divi5-dynamic`.

## Open items

- Done (23:05): the user set the date field's Return Format to `Y-m-d H:i:s`; single movie pages now show "16 October 2026, 2:17 pm" and the archive "16 Oct, 2026".
- The theme's sidebar and footer widgets ("Archives", "Categories") show under Theme Builder bodies on movie pages. Cosmetic; a global footer in the Theme Builder would replace them.

## Roadmap (not started)

| Next step | Why | Needs |
|---|---|---|
| Moving date windows ("from today") | Divi's `metaQuery` takes fixed values only (T17) | Out of scope here (the user's Date Conditions for Divi plugin) |
| Font and number variable references | Use the site's own fonts and sizes without writing globals | Blocked: the user cannot save variables in 5.15.1 (possible Divi bug); retest after a Divi update |
| Presets | Agents could apply existing presets | Reading only is allowed; writing is gated |
| Divi upgrade check | Divi ships weekly; skills say "tested on 5.15.1" | Rerun T1, T6, T10 and T12 after each minor release |
| Real sites | Use the skills on Foodbank and Bristol Charities staging builds | Staging sites with Novamira, never live |

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
2. Check the connection: `novamira/execute-php` returning `ET_BUILDER_VERSION`. If the Novamira tools are missing, Claude Desktop's Novamira extension needs switching on and the app restarting.
3. Compare the stored skills with `skills/` by md5 before editing either.
4. Pick from Open items or the Roadmap. Follow the standing approvals and gates in CLAUDE.md, and log every site write in docs/08.
