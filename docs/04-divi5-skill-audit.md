# 04. Audit of divilovewp/divi5-skill

Repo: https://github.com/divilovewp/divi5-skill (reviewed 14 October 2026). Purpose in this project: source material for Novamira skills about Divi 5.

## Verdict

Legitimate, careful and useful as source material. Not usable as an install: it is about two months behind the site (Divi 5.11.1 versus 5.15.1), part of it sells the author's paid plugin, and its workflow rules conflict with agentic use. Adapt selectively and verify each rule on the Studio site before trusting it.

## What was read

Read in full or in large part (via fetch, so summarised by the fetch tool and possibly imperfect): `SKILL.md`, `README.md`, `CHANGELOG.md`, `DIVI5-BASE.md`, `DIVI5-LAYOUT.md`, `DIVI5-MODULES-CONTENT.md`, `DIVI5-CONNECT.md`, `DIVI5-WORDPRESS.md`, `DIVI5-COVERAGE.md`, `KNOWN-CAVEATS.md`, `build-skill-zip.ps1`, `LICENSE`.

Not read: `DIVI5-DESIGN-PROCESS.md`, `DIVI5-STYLING.md`, `DIVI5-MODULES-INTERACTIVE.md`, `-MEDIA`, `-DATA`, `-DYNAMIC`, `-WOOCOMMERCE`, `DIVI5-PRESETS.md`, `DIVI5-PATTERNS.md`, `WORKFLOW.md`, `MODULE-TEMPLATE.md`, `history/`, `docs/wiki/`. Read the styling, dynamic and presets files before basing skills on them.

Could not read: commit dates and the content of the open pull requests (GitHub's API and commits page blocked the fetch tool).

## Facts [Web]

- Owner `divilovewp`; "Created and maintained by Shashank Gupta (@shashankgupta-dev) of divilove.com". Licence MIT, copyright 2026 Shashank Gupta.
- 60 commits, 28 stars, 4 forks. Pull requests: 4 open (#6, #7, #8 from contributor MDHMatt opened 26 August; #9 from spetrum opened 9 September), 5 closed. The maintainer's last release is 0.6.9 on 26 August 2026, so the open PRs have gone unreviewed for 5 to 7 weeks.
- Versioning tracks the Divi Builder schema: 0.6.9 targets Divi 5.11.1. Earliest recorded version 0.3.0 (Divi 5.0.x). Releases roughly every one to three weeks through summer 2026.
- Files are Markdown reference files plus `build-skill-zip.ps1`, which only zips `SKILL.md`, `README.md` and `DIVI5-*.md` (forward-slash entry names). It reads local files, writes one zip, makes no network calls and runs no external commands. No other executable code. Python and PHP appear only as sample snippets inside the docs.
- Commercial context: divilove.com sells **Divi Connect**, a WordPress plugin (Freemius checkout; Free plan on unlimited sites; Pro $39 a year for 1 site, $59 for 5, $89 for 1,000; lifetime licences sold out). The skill is free and standalone, and is bundled inside the plugin. About a third of the content (`DIVI5-CONNECT.md`, parts of `DIVI5-PRESETS.md` and `DIVI5-PATTERNS.md`) documents Divi Connect's own tools and tiers.

## Credibility signals

- The changelog is candid and corrects its own errors with measurements: the `phoneWide` breakpoint was wrongly documented as the mobile breakpoint (fixed in 0.6.7); `style:["uppercase"]` emits no `text-transform` and only `capitalization` does (0.6.6); `htmlAttributes` needs the breakpoint outermost (0.6.6); a font variable must use `{"type":"content"}` (0.6.6); an earlier claim that Divi 5.10 added no schema was wrong because two modules shipped unannounced. The method stated is to diff Divi's `ModuleLibrary/` between versions rather than trust release notes.
- Claims carry a status per feature (render-confirmed, source-verified, untested) in `DIVI5-COVERAGE.md`, and a failure list in `KNOWN-CAVEATS.md`.
- Outside contributors submit render-verified fixes.
- Not independently reproduced by this project yet. The author's tests ran on the author's own local site.

## Concerns

1. **Stale.** Last release targets 5.11.1. Divi 5.12 to 5.15.1 are not covered (5.12 added the Divi AI Agent; 5.15 made global modules into components and added the Slot module). The `divi/global-layout` guidance is probably stale: the live site registers `divi/layout` and `divi/global-slot` but not `divi/global-layout` [Run].
2. **Funnel.** The skill's live-publish mode assumes Divi Connect's REST API, an API key header (`X-Divi-Connect-Key`) and Pro-tier features (size variables, Theme Builder, WooCommerce, 490 presets). None applies to a Novamira setup.
3. **Injection surface.** `DIVI5-CONNECT.md` tells the model to fetch external URLs with `divi_import_reference` and to send an API key. Drop it.
4. **Workflow gates.** `SKILL.md` makes discovery questions and a plan-approval step mandatory ("WAIT"), and tells the model it is the number one thing models get wrong. This conflicts with the user's preference for one question through the choice tool and with unattended runs. Replace with a single short confirmation when the brief is ambiguous.
5. **Sample credential.** `DIVI5-WORDPRESS.md` contains an application-password-style string in sample Python. Do not copy it. It looks like a local-site credential, but it is sloppy for a public repo.
6. **Mixed audiences.** Some rules exist because of the author's transport (the claude.ai connector corrupts multibyte characters and keeps JSON `\uXXXX` escapes literally). They may not apply to PHP-based writes. See "Escaping" below.

## Cross-checks done

| Claim | Check | Result |
|---|---|---|
| Only Phone, Tablet and Desktop are on by default; `phoneWide` emits nothing until enabled | Local docs (`customize-the-divi-5-s-default-breakpoints.md`) and live option | Agrees on defaults [Docs + Run]. The option `et_divi_builder_breakpoints` is unset. |
| Media-query direction (tablet `max-width: 980px`, phone `max-width: 767px`, ultraWide `min-width: 1440px`) | Local docs | **Conflict.** The docs text says Phone, Phone Wide, Tablet and Tablet Wide use min-width and Widescreen and Ultra Wide use max-width. The skill's table is a render measurement. Unresolved, settle with test T2. |
| Module list (84 to 86 modules) | Block registry on the live site | 88 `divi/*` types registered; names match the skill's except `divi/global-layout` (missing) and new `divi/layout`, `divi/global-slot`. Third-party and Woo modules absent for lack of plugins. |
| Required post meta `_et_pb_use_builder`, `_et_pb_page_layout`, `_et_pb_use_divi_5` (Theme Builder case) | Existing meta keys on site and the earlier review | The site has only the first two. The earlier review's `set-builder` writes six keys (`_et_pb_use_builder`, `_et_pb_use_divi_5`, `_et_pb_page_layout`, `_et_pb_side_nav`, `_et_pb_built_for_post_type`, `_et_builder_version`). Confirm with a Visual Builder baseline page (T1). |
| `divi/placeholder` wrapper required | Local docs silent, apart from a changelog line about placeholder block validation on WordPress 7.1 (found by the earlier review) | Plausible, needs T1. |
| `modulePreset` value form | Skill says a string key; the earlier review says docs show a string and the repo uses an array | Unresolved. |

## Escaping (important for the PHP route)

- divi5-skill says rich-text values go in as raw HTML (`<p>`), and that pre-escaping (`&lt;` or `\u003c`) ends up visible on the page, because its connector escapes server-side and a JSON `\u003c` survives the transport verbatim.
- The `doughoseck/divi5-builder` compiler (reviewed in `divi5-builder-review.md`, since deleted) does the opposite for its REST writes: it escapes `\`, `"`, `<`, `>`, `&` and `--` into JSON unicode escapes, which it measured as the form Divi stores.
- Both are transport-specific. Over Novamira the write is PHP inside WordPress, so there is no connector. [Inferred] The safest method is to build block attributes as PHP arrays and serialise them with WordPress core (`serialize_block()` or `serialize_blocks()`, which apply WordPress's own comment-safe JSON escaping), then compare the stored result with a Visual Builder-saved page. Not yet tested.

## Keep, adapt, drop

| File or section | Action |
|---|---|
| `DIVI5-BASE.md` (hierarchy, non-negotiable rules, silent-failure list, validation checklist, breakpoints) | Keep and condense into `divi5-core`. Re-verify each rule. |
| `DIVI5-LAYOUT.md` (section, row, column, group, grid) | Keep for `divi5-styling` or core. Drop `global-layout` and the Python helpers until re-checked. |
| `DIVI5-MODULES-CONTENT.md` and other `-MODULES-*` | Keep, split into one or two module skills. Each shape needs a render check or a comparison with Divi's `module.json`. |
| `DIVI5-STYLING.md`, `DIVI5-PRESETS.md` | Read first. Keep the attribute paths and the hover shape. Presets and variables are where Divi changes fastest. |
| `KNOWN-CAVEATS.md`, `DIVI5-COVERAGE.md` | Keep as a compact "silent failures" table. |
| `DIVI5-WORDPRESS.md` | Reuse only the meta and cache facts (dynamic-assets meta, `page-template-blank.php` suppressing Theme Builder chrome). Drop REST, mu-plugin and credential material. Add Novamira PHP recipes instead. |
| `DIVI5-CONNECT.md` | Drop. |
| `DIVI5-DESIGN-PROCESS.md` | Read. Keep the checkable heuristics and the "avoid AI clichés" ideas if they help. Drop the WAIT gates. |
| `DIVI5-PATTERNS.md` | Optional. Patterns are useful as examples after the core is verified. |

## Adaptation requirements

1. Keep the MIT notice (NOTICE.md).
2. Novamira skills are single documents. Split into 2 to 4 skills with sharp descriptions, each well under 5,000 words.
3. Refresh for 5.15.1 and verify on the site. Stamp `builderVersion` from `ET_BUILDER_VERSION` at runtime, never a literal.
4. Replace Divi Connect workflows with Novamira PHP recipes (docs/06).
5. Write in the user's style (docs/06 and CLAUDE.md).
