# 06. Recommendations and plan

## Summary

1. Use the free Novamira route first. It runs PHP inside WordPress, so it avoids the REST and application-password problems the user hit with other tools. The cost is that Claude writes Divi 5 block markup by hand, which is error-prone without good guidance.
2. Provide that guidance as 2 to 4 small Novamira skills adapted from `divilovewp/divi5-skill`, plus a short User Context. Verify every rule on the Studio site before it goes into a skill.
3. Run the verification tests below before writing any skill. The most valuable is T1: a baseline page saved by the Visual Builder, compared with a page written through Novamira.
4. Decide on Novamira Pro only after T1 and T2 and a few realistic build tasks.
5. Skip Novamira Design for Divi for now (reasons below).

## Recommended order

| Step | Action | Needs user approval to write? |
|---|---|---|
| 1 | Connect Claude Code to the Studio site (CLAUDE.md). | Creates an application password in wp-admin (user does it). |
| 2 | Ask the user to create a baseline page in the Visual Builder: one section, row, column, heading, text, button, saved. Record its `post_content` and all postmeta (read-only). | No site write by Claude. |
| 3 | Run T1 to T6 on throwaway draft pages. | Yes (draft pages, log in docs/08). |
| 4 | Write the User Context (draft below). Re-read `novamira/agent-context` to confirm it appears. | Yes. |
| 5 | Write `divi5-core` v0.1 as a Novamira skill. Test with a fresh session on the sample prompts below. Iterate. | Yes. |
| 6 | Add `divi5-styling` and `divi5-modules` after their rules are verified. | Yes. |
| 7 | If building pages shows repeated friction, add the sandbox helper. | Yes. |
| 8 | Decide on Pro and on design tokens. | Decision. |

## Verification tests (draft pages on the Studio site only)

Use `novamira/execute-php`. Build pages as `draft` and delete them afterwards, logging each in docs/08. Do not touch existing pages.

**T0 baseline (read-only).** From the user's Visual Builder page: dump `post_content`, all postmeta, the `builderVersion` it stamped, the wrapper form, how `<p>` and quotes are escaped, and which attributes the builder adds by default. This is ground truth for every later comparison.

**T1 minimal page and round trip.**
1. Create a draft with the skeleton in docs/05 and the meta found in T0.
2. Create a second draft built from PHP arrays with `serialize_block()` (or `serialize_blocks()`), same content.
3. Compare both with the baseline: block names, attribute shapes, escaping, whitespace.
4. Ask the user to open each draft in the Visual Builder. Pass: the modules appear and are editable (not blank). Then save from the builder without changes and diff `post_content` before and after.
Reading the result: a pass means hand-written markup is viable and the method (hand-written or `serialize_block`) can be chosen. A blank builder or rewritten content means the skill must change its method, or the free route is weak.

**T2 breakpoints.** One heading with a different `size` on `desktop`, `tablet`, `phone` and `phoneWide`. Fetch the page and read the emitted CSS (Divi static file under `wp-content/et-cache/` or inline). Settles the media-query direction conflict between the local docs and the skill.

**T3 global modules and components.** In the builder, save a section as a global module or component. Read its block name and how a page references it. Record what replaced `divi/global-layout` and how `divi/global-slot` is used.

**T4 dynamic-assets cache.** Add an accordion and tabs by PHP to a page that already rendered once. Check whether their scripts load. Repeat after deleting `_divi_dynamic_assets_cached_modules` and clearing `wp-content/et-cache/`. Decide whether the helper must always clear them.

**T5 schema cross-check (offline, read-only).** Find Divi's own module metadata under `Projects\divi5-builder\Divi` (search for `module.json` and the module library folders). For heading, text, button, image, blurb, cta, row, column, section, check every attribute path the skill claims. Note any difference at 5.14.0 versus 5.15.1.

**T6 silent-failure rules.** Quick renders: `capitalization` versus `style:["uppercase"]`, `htmlAttributes` shape, button hover shape, `attrs` wrapper, `body` versus `content`. Confirms the skill's claims on 5.15.1.

Record pass or fail for each test in a new `docs/09-test-results.md`, and log the draft pages created in docs/08.

## Draft User Context (about 150 words)

Save to `novamira_instructions_content` (and enable) only after the user agrees. Adjust the skill name after the skills exist.

```
This is a local Divi 5 development site. Divi 5 is the page builder for every page, template and layout, so do not ask which builder or approach to use. Use native Divi 5 modules and settings, never a code module or injected HTML, CSS or JavaScript, for layout. Read the version from ET_BUILDER_VERSION and stamp it as builderVersion on every module. Before writing Divi block markup, load the divi5-core skill. Check the local Divi 5 documentation and the live site (the block registry and saved Divi data) before relying on remembered syntax. Build new pages as drafts and never overwrite an existing page unless asked. Do not create or activate a Novamira design, and do not write to Divi's global colours, variables or presets, unless asked. After any write, read it back and report what changed. Never modify the Novamira plugin or the application passwords of the connected user.
```

Why each line: Novamira's system context otherwise makes the agent ask which builder to use, the built-in design skill can trigger a DESIGN.md capture and save before any visual build, and the connection-safety line protects the link.

## Skill design (Novamira skills are flat single documents)

Aim for each body under about 2,500 words. Descriptions are the only trigger text, so make them concrete.

1. **`divi5-core`** (write first).
   Description: "Rules for writing Divi 5 page content as block markup through PHP: structure, builderVersion, required post meta, safe draft creation, cache clearing and verification. Use when the user asks to build, edit, rebuild, copy or inspect a Divi 5 page, section, layout or template."
   Body: preflight (read `ET_BUILDER_VERSION`, read the existing page and a registry check before editing); hierarchy and non-negotiable rules (docs/05); how to build markup (method chosen in T1); creation recipe (draft, meta, template); verification (read back, render check, ask the user to open the draft in the builder); cache clearing (T4); behaviour (one question at most, drafts only, log); a silent-failure table.
2. **`divi5-styling`**: decoration paths (background, spacing, border, typography), hover shape, breakpoints, variables, presets, grid and flex. Description names the triggers "styling", "responsive", "mobile", "spacing", "colour", "preset".
3. **`divi5-modules`**: verified attribute shapes for the common modules (heading, text, button, image, blurb, cta, icon list, accordion, tabs, contact form, testimonial), each marked with the version it was tested on. Split into content and interactive if it grows.
4. **`divi5-dynamic`** (later): Loop Builder, post filter, ACF and dynamic content. Only after the rest is verified. The user's own plugin "Date Conditions for Divi" targets ACF date filtering in loops, so loops are likely to matter.

Rules for writing them:
- Keep the licence attribution file with the content (NOTICE.md). Skills are stored in WordPress, so add a one-line credit at the end of each body.
- No meta sections, changelog or "when to use" prose. State a tested version on each module shape, and say "verify on this site" for anything untested.
- Replace every Divi Connect workflow with Novamira recipes. Remove the WAIT gates.
- Create with `novamira/skill-write` (`on_conflict` left at `fail`), verify with `skill-get`, patch with `skill-edit`.

## Sandbox helper (optional, step 7)

Novamira's docs endorse shared helper code saved as a sandbox file and referenced by path from a skill. PHP can only be written to `wp-content/novamira-sandbox/` with `write-file`. Spec for a helper (do not write it until T1 to T4 give the facts, and read Novamira's sandbox and crash-recovery docs first):

- `nova_divi_create_draft_page(string $title, array|string $blocks, array $opts = []): array`
  - Creates a post of type `page` with status `draft` (never published, never overwriting).
  - Builds `post_content` with the method T1 selects, wrapped as the baseline page is.
  - Sets the full builder meta found in T0 and clears `_divi_dynamic_assets_cached_modules` and Divi's static CSS as T4 decides.
  - Returns `{ id, edit_url, preview_url, builder_url, meta_written, content_hash }` and refuses if a page with the same title and slug exists unless an explicit flag is passed.
- `nova_divi_read_page(int $id): array` returns content, meta and a parsed block tree for diffing.
- Acceptance: a Novamira session can create a valid draft with one call, the draft opens in the builder, and a unit test through `execute-php` passes against the baseline.

Why: it cuts tokens and errors, and puts the unrisky defaults (draft, meta, cache) in code instead of prose.

## Design tokens

Novamira Design's own skill says tokens that exist only in a DESIGN.md are invisible to a page builder and that site globals should not be hand-written when no sync ability exists. Free Novamira has no Divi sync. Divi 5 has its own variables, colours and presets. Recommended default: **skip Novamira Design for Divi sites for now** and use Divi's own system, with User Context telling the agent not to create or activate a design. Alternatives the user can pick later:
1. Capture the site's look as a DESIGN.md for guidance and for `check-design` linting of rendered pages only.
2. Materialise tokens into Divi 5 variables on request. This writes site globals outside Novamira's advised path. The divi5-skill documents the stores, but the earlier review found that Divi's colour and variable stores changed (`et_global_colors` is Divi 4; Divi 5 uses `et_divi` global data) and that preset behaviour changes weekly. Treat as high risk.

## Free versus Pro

Buy Pro (from 49 euros a year, Personal) only if, after T1 to T6 and three realistic builds, one of these holds:
- Hand-written markup is too fragile (T1 shows frequent invalid output or builder rewrites).
- You need single-module edits of existing pages, Loop Builder and dynamic content, Theme Builder layouts or design-system writes, which Pro provides as maintained abilities.
- Memory between sessions would save more time than it costs.

Counter-consideration: Elegant Themes ships its own Divi AI Agent in Divi 5.12 (paid) and has announced an official MCP with no date. Check that before committing to a yearly licence.

## Sample tasks for judging the skills (fresh session each)

1. "Create a draft landing page with a hero (heading, text, button), three feature blurbs and a call to action."
2. "Copy page X to a new draft and add an FAQ accordion at the bottom."
3. "Make a two-column row, image left and text right, that stacks on phone."
4. "Explain what is on page Y and list any module that is not Divi native." (read-only)
Score: valid markup, opens in the builder, no blank modules, native modules only, version stamped, draft status, meta correct, one question at most.

## Risks and cautions

- Novamira gives unrestricted admin-level PHP, database and file access. Local Studio only. Never connect it to a live site.
- User Context and skills are shared by every administrator and read as instructions. Imported text (including divi5-skill content) must be reviewed first.
- The Studio database is SQLite. Avoid MySQL-only SQL in helpers.
- Divi releases weekly. Re-verify preset, variable and component behaviour at each minor release. Record the tested version in each skill.
- The user's earlier finding that REST-injected content shows blank in the Visual Builder has not been re-tested through PHP. T1 answers it.
- Cache behaviour on the live hosting (LiteSpeed Cache) cannot be tested on Studio. Do it on a staging copy later.

## Do not

- Do not install divi5-skill, Divi Connect or any custom REST plugin.
- Do not paste divi5-skill files wholesale into Novamira.
- Do not write to Divi global colours, variables or presets without an explicit request.
- Do not make any write before the user has agreed to it.
