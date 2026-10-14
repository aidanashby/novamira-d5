# 10. Decision memo: free route, Novamira Pro, design tokens

14 October 2026. Site: Studio `divi-5`, Divi 5.15.1, Novamira 1.12.9 free. Evidence in docs/09; every site write in docs/08.

## Decisions (user, 14 October 2026)

- Gate (e): **no Novamira Pro.**
- Gate (d): **option 1**, keep Divi globals as the source of truth, skip Novamira Design, agents reference global colours read-only.

## Recommendation

1. **Do not buy Novamira Pro now.** The free route builds, copies and edits Divi 5 drafts reliably with five verified skills, including Theme Builder, Loop Builder and ACF dynamic content. Presets were tested later (T19) and also work; preset writes stay behind the user's approval.
2. **Design tokens: keep Divi as the source of truth, and skip Novamira Design.** Agents read the site's global colours and reference them; they do not write them.

## Update (14 October 2026, late evening)

After the decision, the free route was also tested on Theme Builder, Loop Builder, ACF dynamic content and a custom post type (Movies), the areas listed below as Pro's main additions. All worked. The decision stands with more evidence behind it.

## How far the free route got [Run]

- **Storage method solved.** Building block arrays and serialising them with WordPress's `serialize_blocks()` reproduces the Visual Builder's storage byte for byte (T1). Parse, modify and serialise round-trips builder pages exactly, including components (editing round trip).
- **The earlier blank-builder problem did not occur.** Every draft written through PHP opened, edited and saved in the Visual Builder (T1, T7, S2, S3, T9, S1b).
- **Agents succeed from the skills alone.** Six fresh agents limited to Novamira tools completed every task: landing page, copy plus FAQ, image and text row, read-only explanation, and a styled contact page. They loaded the right skills unprompted, asked no unnecessary questions, wrote drafts only and stamped versions correctly. One round of fixes was needed (use the named module, column layouts).
- **Covered and verified:** structure, meta, caches, row layouts that stack on phone, 14 modules, the main styling groups, hover, breakpoints, and global colour references.
- **Friction seen:** skills cannot contain backslashes (Novamira strips them, worked around); an agent skipped the render check once; module shapes outside the skills need `module.json` reading or a builder reference page.

## What Pro would add, against what failed

| Pro feature | Needed so far? |
|---|---|
| Build pages as real modules | No. Free route already produces builder-identical modules. |
| Single-module edits on existing pages | No. Parse, modify, serialise does this exactly. |
| Loop Builder, dynamic content (ACF, post data) | No. Tested later the same day (T14 to T16): group, blurb and column loops over a custom post type, ACF text, WYSIWYG, image, URL and date fields, `excludeCurrentPost`, pagination. Loop filters by meta value are still untested. |
| Global presets, design system writes | No. Reading, applying, creating and editing module and group presets worked (T19, under a one-day permission); the skill keeps writes gated. |
| Theme Builder layouts | No. Editing header and footer layouts and creating single and archive templates for a post type all worked (T13, T14, T16). |
| Memory between sessions | No. Skills and User Context cover it. |
| Read Mode for production | Useful only if you want Novamira on a live site, which the project rules forbid. |

No failure in testing would have been fixed by Pro. Also check Elegant Themes' own route before any licence: the Divi AI Agent shipped in 5.12, and an official MCP has been announced without a date.

## Design-token options

1. **Skip Novamira Design; reference Divi globals (recommended).** Agents list colours with `GlobalData::get_global_colors()` and reference them as `$variable(...)$` (verified, renders `var(--gcid-...)`). No writes to globals. Costs nothing and keeps Divi's own system authoritative.
2. **DESIGN.md for guidance and linting only.** Save a Novamira design without activating it, and run `check-design` on rendered pages. Adds a second description of the brand that can drift from Divi's globals. Saving a design needs your approval.
3. **Materialise tokens into Divi variables.** Agents write global colours and variables. High risk: Divi's global data store (`et_divi` key `et_global_data`) and preset behaviour change between releases, and free Novamira has no sync ability. Needs your approval each time.

## Still open

- Not tested: loop filters by meta value or term, presets, font and number variables, published-page cache behaviour, LiteSpeed on Krystal staging.
- Open items and roadmap: docs/00.
- Test drafts moved to the bin (docs/08).
