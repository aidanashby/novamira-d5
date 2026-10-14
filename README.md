# novamira-d5

Handoff pack for the Claude, Novamira and Divi 5 project. Written by Claude in a chat session on 14 October 2026 so that Claude Code can continue from where that session stopped. This folder is the local clone of a private GitHub repo.

## Goal

Let Claude build and edit Divi 5 pages on WordPress sites through the free Novamira plugin (an MCP server that runs inside WordPress), starting on a local WP Studio site. Three deliverables:

1. A working, verified connection from Claude to the local Divi 5 site. Done for Claude Desktop. Claude Code is not connected yet.
2. Divi 5 knowledge delivered to the agent as Novamira skills, adapted from the MIT-licensed `divilovewp/divi5-skill` and checked against Divi 5.15.1, the local Divi docs and the live site.
3. A decision on whether Novamira Pro (which adds Divi 5 module-level tooling) is worth buying, based on how far the free route gets.

## Status at handoff from the chat session (14 October 2026, about 15:50 BST, superseded by the status below)

Done:
- Novamira 1.12.9 (free) is installed on the local Studio site with AI Abilities enabled. Claude Desktop is connected through the Novamira MCP bundle (application password). Read-only probes worked.
- Research is written up: Novamira configuration surface (docs/03), audit of `divilovewp/divi5-skill` (docs/04), distilled Divi 5 authoring notes (docs/05), recommendations and test plan (docs/06).

Not done:
- Nothing has been written to the Studio site. No skills, no User Context, no design, no test pages. See docs/08.
- Claude Code is not connected to the site yet (instructions in CLAUDE.md).
- The skill's claims are not yet verified on Divi 5.15.1 (test plan in docs/06).
- Divi 5.12 and 5.13 release notes were not reviewed. Divi's own `module.json` schemas were not cross-checked.
- The OAuth sign-in route failed on Studio (docs/02). It is not needed, because the application password route works.

Open decisions (the user had no preference when asked, so Claude Code raises each at a gate, see CLAUDE.md, "Standing approvals"):
1. Which writes to make first. Recommended order is in docs/06.
2. How design tokens should work with Divi (skip Novamira Design, capture a DESIGN.md for guidance and lint only, or materialise tokens into Divi variables). Recommended default: skip for now.
3. Free versus Pro, after the verification tests.

## Status (14 October 2026, 17:00 BST, paused by the user)

Done (details in docs/09, every site write in docs/08):
- Step 1: this Claude Desktop Code-tab session reuses Claude Desktop's Novamira connection (user's choice). No separate application password.
- Steps 2 and 3: T0 to T7 run and passed. Main results: `serialize_blocks()` from PHP arrays matches the Visual Builder byte for byte; parse, edit, serialise round-trips exactly; five meta keys are enough; drafts open, edit and survive a builder save; `divi/global-layout` is current for components; clear caches with `ET_Core_PageResource::remove_static_resources($id, 'all')`. docs/05 has a status table.
- Step 4: User Context written and verified (gate c approved). `divi5-core` skill written (post ID 30); source in `skills/divi5-core.md`.

Open, in order (resume here):
1. Done 20:15: the 2-byte difference was the local copy (line 19 had literal characters where the Write tool had decoded `` escapes). Local file now matches the stored skill (md5 043f1d0f...). `divi5-core` is listed in `agent-context`. Studio update moved PHP to 8.3.35.
2. User opens draft ID 28 "T7 Skill recipe two columns" in the Visual Builder.
3. Step 5: run the four sample tasks (docs/06) in a fresh session using only what the site agent sees; score; up to three fix rounds; user judges the drafts.
4. Step 6: read the unread divi5-skill files (docs/04), then write `divi5-styling` and `divi5-modules`.
5. Steps 7 and 8: sandbox helper only if friction justifies it; `docs/10-decision-memo.md`; gates (d) design tokens and (e) free versus Pro.
6. Clean-up: delete Claude's drafts 9, 24, 25, 26, 28 and the sample-task drafts. The user's pages 10, 18 and library item 22 stay unless the user says otherwise.

Learnings not to lose: skill storage strips all backslashes (docs/09); Save to Library is under the section's three-dot hover menu in 5.15.1, not right-click; Divi skips saving when nothing changed; the local docs describe breakpoint direction wrongly.

## Reading order

1. `CLAUDE.md` (rules for working in this project, how to connect)
2. `docs/01-goal-and-environment.md` (the site, the connection, local paths)
3. `docs/02-session-log-and-learnings.md` (what happened, what broke, what was learned)
4. `docs/03-novamira-reference.md` (every Novamira option, ability, storage location and constraint found)
5. `docs/04-divi5-skill-audit.md` (is the repo legitimate, what to keep and drop)
6. `docs/05-divi5-authoring-notes.md` (distilled Divi 5 rules, all unverified until tested)
7. `docs/06-recommendations-and-plan.md` (what to do next, test plan, draft User Context, skill split, helper spec)
8. `docs/07-sources.md` (links)
9. `docs/08-site-change-log.md` (log every write to the site here)
10. `NOTICE.md` (third-party licence attribution)

## Evidence labels

Used throughout, following the convention in `Projects\divi5-builder\divi5-builder-review.md`:
- [Run] observed by running something on the Studio site or a tool.
- [Docs] taken from Novamira's documentation or the local Divi 5 docs.
- [Web] taken from a web page, with the URL in docs/07.
- [Skill] a claim made by divilovewp/divi5-skill, not yet verified here.
- [Inferred] Claude's reasoning, not verified.

## First actions for Claude Code

1. Read `CLAUDE.md` and docs 01 to 06.
2. Connect Claude Code to the Studio site (CLAUDE.md, "Connecting Claude Code").
3. Lead the whole plan in docs/06 (CLAUDE.md, "Plan to execute"), stopping only at the gates listed under "Standing approvals". Log every site write in docs/08.
4. Write skills only with rules that passed the tests. Keep this README status current.
