# Novamira + Divi 5: Design and Content Workflow Expansion

**Project:** `novamira-d5`  
**Purpose:** Implementation brief for Claude Code  
**Status:** Proposed work. Inspect the current repository and site before making changes.

## 1. Mission

Extend the existing `novamira-d5` skills and workflow so Claude can produce more deliberate, consistent, accessible and content-led Divi 5 websites. Improve the core workflow where needed, then add focused capabilities that compose with the six existing skills.

The goal is to make the existing setup more capable and dependable without replacing its proven authoring approach or adding unnecessary infrastructure. Use the free Novamira plugin. Do not assume Novamira Pro is available or required.

Work autonomously on investigation, design decisions and implementation details within the established project rules. Ask for approval at the explicit decision gates in this brief. Do not ask the user to decide routine implementation details that can be resolved through repository evidence, Divi documentation, tests and sensible engineering judgement.

## 2. Read first and establish the baseline

Before changing anything:

1. Read the repository `README.md`, `CLAUDE.md` and `docs/00-status-and-roadmap.md`.
2. Read the relevant existing skill files and the supporting docs in `docs/01` to `docs/10`. Avoid loading everything repeatedly when a focused subset is sufficient.
3. Read the applicable parent `Projects\CLAUDE.md` instructions.
4. Inspect the current git status and recent changes. Preserve unrelated user work.
5. Confirm the active local WordPress Studio site, Divi version, WordPress version and Novamira configuration. The last recorded test environment was `http://localhost:8881`, WordPress 7.1.3 and Divi 5.15.1, but verify current versions rather than assuming they remain unchanged.
6. Map the six existing skills and identify the current source-of-truth files, mirrored skill copies, test methods and documentation conventions.
7. Summarise the current baseline, any relevant constraints, and a concise implementation plan before making substantive changes.

The existing authoring system has already demonstrated Divi 5 block storage, read-back verification, Visual Builder compatibility and browser checks. Build on that evidence. Do not redesign working foundations without a clear, tested reason.

## 3. Existing project constraints

These rules are part of the task.

- **Local development only.** Use the approved local WordPress Studio site. Never make changes to a live or other non-local site without explicit approval.
- **Divi 5 only.** Use Divi 5-native structures and the existing verified authoring approach. Do not introduce Divi 4 assumptions.
- **Free Novamira only.** Do not depend on Novamira Pro or silently introduce paid services.
- **Global design tokens are authoritative.** Treat the site's existing Divi global design tokens as the source of truth. Do not alter global settings or tokens without explicit approval.
- **Draft-first workflow.** Create draft pages for tests and proposed work. Publish only when explicitly asked.
- **No destructive cleanup by assumption.** Never delete or unpublish existing pages, templates, content, assets or settings. Delete only temporary drafts created by this task, and only within the project's existing approval rules.
- **Explicit approval gates.** Ask before changing plugins, themes, global settings, unapproved settings, or anything outside the approved local scope. Use the established choice/approval mechanism where available.
- **Protected configuration.** Do not touch the Novamira plugin itself or connected user 1 credentials. Do not commit secrets or `.claude/settings.local.json`.
- **Write verification is mandatory.** After every WordPress write, read the data back and verify it. Record changes and undo steps in `docs/08-site-change-log.md` according to existing conventions.
- **Keep evidence honest.** Use the project's evidence labels `[Run]`, `[Docs]`, `[Web]`, `[Skill]` and `[Inferred]`. Do not describe a capability as tested when it is only documented, inferred or instructed by a skill.
- **Preserve project hygiene.** Follow existing test-first, verification and repository conventions. Do not create scratch directories in the Projects folder. Avoid adding dependencies, helper services or abstractions unless they solve a demonstrated need.
- **Keep mirrored skills in sync.** Follow the existing process for updating skill source files and their stored copies. Verify byte identity or the repository's established equivalent after changes. Skill text storage may strip backslashes, so test the actual write/read path.
- **Do not overfit to the test site.** Separate general Divi authoring knowledge from site-specific tokens, content, assets and preferences.

## 4. Product principles

Apply these principles across all new and existing workflows.

1. **Purpose before layout.** Understand the audience, task, message, desired action and constraints before selecting a page structure.
2. **Design with intent.** Make deliberate decisions about hierarchy, rhythm, typography, contrast, density, imagery, alignment and interaction. Avoid defaulting to a generic sequence of hero, three cards and CTA.
3. **Content determines structure.** The page architecture should serve the supplied information and user journey. Do not force every brief into one template.
4. **Existing design system first.** Reuse global tokens, presets, approved components and established patterns where appropriate. Do not create near-duplicate tokens or presets to solve a local layout problem.
5. **Preserve versus redesign must be explicit.** Auditing for structural consistency must not silently change the intended visual design.
6. **Accessible by construction.** Consider semantic structure, keyboard use, visible focus, text alternatives, contrast, link purpose, form labels, heading hierarchy and responsive reflow during authoring.
7. **Evidence over confidence.** Inspect the stored page, rendered output and browser behaviour. A successful write or plausible screenshot alone is not proof that the task works.
8. **Human approval at consequential gates.** In particular, the user approves a new site's visual direction before Claude builds its other pages, unless usable design examples or explicit instructions already establish that direction.
9. **Compose rather than duplicate.** New skills should use the verified `divi5-core`, `divi5-styling`, `divi5-modules`, `divi5-theme-builder`, `divi5-dynamic` and `divi5-presets` skills. Avoid copying large amounts of their implementation knowledge into each new skill.
10. **Separate completion from verification.** Report what was done and what was actually verified, including unresolved checks and limitations.

## 5. Priority 1: Make design approval a standard new-site workflow

### Desired behaviour

When asked to set up a new website, first establish whether the user has supplied usable design references, an existing site design to reproduce, a sufficiently specific approved design brief, or an existing design system that should govern the work.

- **If suitable design examples or an approved design direction exist:** use them as the authority. Do not require a redundant showcase approval stage.
- **If the visual direction is not established:** create a tailored **design showcase draft page** before building the site's other content pages. Then stop and request the user's review and approval of the design direction.
- Other non-page setup work may continue if it does not prejudge the unapproved design. Do not build further content pages until the showcase is approved or the user explicitly changes the instruction.
- If the user asks to skip the showcase for a particular project, respect that instruction.

### What the showcase should demonstrate

The showcase is a practical design approval artefact, not a dump of every registered module. Select representative patterns likely to be useful for this site's purpose and content. Depending on the brief, this may include:

- Heading hierarchy, body copy, links and buttons.
- Hero or introductory treatments, with alternative treatments only where a genuine design decision needs comparison.
- Common section and row structures, including varied column arrangements.
- Image and text combinations, cards or other repeated content patterns.
- Lists, statistics, quotes, calls to action, notices or other content-specific patterns.
- Relevant forms, accordions, tabs, navigation or other interactive modules when likely to be used.
- Backgrounds, borders, spacing, typography, colour use, imagery and responsive behaviour.
- A representative example of any distinctive component or content type central to the brief.

Choose a useful, manageable range based on the site, not a fixed exhaustive checklist. Demonstrate the design system through real-looking sample content. Label the page clearly as a design showcase and keep it in draft status.

Use existing global tokens and approved presets as the baseline. If the design requires a change to global settings, stop and request approval rather than changing them silently.

### Approval and handover

Present the draft page for browser review, including desktop and mobile checks. Ask the user to approve the overall visual direction or specify changes. Capture agreed decisions so later pages can reuse them consistently.

Approval of the showcase means approval of the **visual direction and demonstrated patterns**, not automatic approval of factual content, final copy, accessibility compliance, or publication.

### Implementation

Create or update the smallest appropriate combination of:
- `divi5-design-system` skill for the design-system workflow and showcase process.
- `divi5-core` and/or the central User Context instructions for the mandatory new-site decision gate.
- Any relevant existing documentation describing how a new site is bootstrapped.

Avoid duplicating detailed module or styling recipes. Use the existing skills for construction and verification.

### Acceptance criteria

- On a new-site task with no supplied design direction, Claude creates a tailored showcase as a draft before creating other content pages.
- Claude stops at the approval gate and does not create other content pages before approval.
- On a task with adequate design examples or an explicit instruction to skip the showcase, Claude proceeds according to that direction.
- The showcase demonstrates a coherent, purposeful design using the existing token system and relevant Divi patterns.
- Desktop and mobile output are inspected; write/read-back and rendered-output verification are recorded.
- Global settings and publication state remain unchanged without approval.

## 6. Priority 2: Automated content-to-page production

### Desired behaviour

Build a workflow that turns a brief and source material into a coherent Divi page, while supporting two operating modes.

**Mode A: supplied-content mode**

- Use the user's supplied copy, documents, structured data and instructions as the source of truth.
- Organise and present the material effectively, preserving meaning and important detail.
- Identify gaps, contradictions, missing facts and unclear calls to action.
- Do not invent missing facts or silently omit important material.

**Mode B: research-and-draft mode**

- Where requested, research missing information using appropriate sources, or draft supporting copy from the brief.
- Keep sourced facts distinguishable from proposed copy and assumptions.
- Record sources for factual claims where appropriate.
- Flag uncertainty and consequential content decisions for review.
- Obtain approval where the user or existing project rules require it.

The user may specify the mode, or Claude may infer the sensible mode from the task. If the distinction materially affects factual accuracy or scope, ask one focused question.

### Suggested workflow

1. Parse the brief into a compact page specification: purpose, audience, primary message, desired user action, required content, calls to action, assets, metadata, constraints, and existing approved patterns.
2. Identify missing information and distinguish blocking gaps from reasonable editorial choices.
3. Map the content to a user journey and page hierarchy before constructing modules.
4. Select the layout and modules based on content and purpose. Reuse approved patterns and design decisions from the showcase where one exists.
5. Build the page as a draft using the existing verified Divi authoring method.
6. Read back stored data, inspect the rendered page, and test desktop and mobile behaviour.
7. Run relevant accessibility checks and check links, images, headings, forms and interactive elements.
8. Report what was created, what sources were used, what assumptions or gaps remain, what passed verification, and what needs approval.

Do not require the same approval process for every minor editorial decision. Respect the user's instructions and existing approval rules, while keeping publication and consequential changes behind their established gates.

### Acceptance criteria

- The workflow produces a coherent draft page from a representative supplied brief and content set.
- It supports both supplied-content and research-and-draft modes.
- It does not invent facts to fill gaps and clearly surfaces unresolved issues.
- It uses the approved design direction, existing tokens and reusable patterns when available.
- It verifies stored data and the rendered page rather than treating successful authoring as sufficient.
- It reports verification results and limitations accurately.

## 7. Priority 3: Component and pattern audit, then proposed conversion

### Purpose

Identify repeated visual or structural patterns that could benefit from a reusable Divi component, shared pattern or existing preset. The objective is to reduce duplication and improve maintainability while preserving the site's intended appearance and behaviour.

### Required behaviour

- Audit and report first. **Do not automatically convert components or rewrite pages.**
- Identify repeated patterns, the pages where they occur, their differences, and the likely maintenance benefit.
- Distinguish genuinely shared patterns from superficial similarities that serve different purposes.
- Check whether an existing global token, preset or component already solves the problem before proposing something new.
- Explain the proposed reusable pattern, which instances would change, what exceptions would remain, and the risks or trade-offs.
- Wait for approval before making conversions.
- After approval, work in small, reversible batches. Verify every write, compare before-and-after rendering, and record undo steps.

Do not assume that every repeated layout should become a component. Reuse is valuable only when it makes future editing more consistent without making legitimate variation harder.

### Acceptance criteria

- The audit produces an actionable report with examples, locations, rationale, proposed approach and confidence.
- It distinguishes design-system inconsistencies from intentional variations.
- No pages are modified during audit-only work.
- After explicit approval, any conversions preserve content, links, responsive behaviour and intended visual appearance, or clearly report deviations.
- Before-and-after verification and rollback instructions are recorded.

## 8. Priority 4: Practical accessibility checks

### Purpose

Add an accessibility workflow that combines automated inspection with browser-based and manual review. Prioritise meaningful user impact and actionable remediation. Do not claim that automated scans establish full accessibility compliance.

### Scope

Cover, where applicable:

- Semantic landmarks and heading hierarchy.
- Accessible names and text alternatives for images.
- Link purpose, button names and empty or ambiguous interactive elements.
- Colour contrast, including text over backgrounds and images where it can be measured reliably.
- Keyboard access, logical focus order, visible focus and common keyboard traps.
- Form labels, instructions, required fields and error feedback.
- Responsive layout, zoom/reflow issues and content clipping.
- Interactive patterns such as menus, accordions, tabs, carousels and dialogs.
- Divi-specific markup and authoring choices that may introduce accessibility issues.

Use existing browser/testing infrastructure if available. Investigate suitable free tools or libraries only where needed. Avoid adding dependencies or building a bespoke testing framework before checking the current environment and the simplest reliable options.

### Reporting and remediation

Each finding should include:

- Severity or user impact.
- Page and element or component affected.
- What was observed and how it was checked.
- Evidence and confidence.
- A practical remediation recommendation.
- Whether the issue can be verified automatically or needs manual judgement.

Offer fixes as part of an authoring task where appropriate. For a standalone audit, report first and obtain approval before broad or consequential changes. Do not change global tokens, site-wide settings or shared components without the applicable approval.

### Acceptance criteria

- A representative page can be checked through both automated tooling and browser/manual review.
- Findings are specific, prioritised and actionable.
- False positives, unsupported checks and unverified assumptions are identified.
- A remediation can be verified after the change.
- Reports explicitly distinguish checks passed, issues found, and checks not performed or not automatable.

## 9. Priority 5: Design-system audit and refactoring

### Keep two modes separate

**Mode A: consistency and refactoring audit**

The default when the user asks for an audit, cleanup, consistency or refactoring. The goal is to make the implementation more logical and maintainable while preserving the intended visual design and user experience.

Look for:

- Inconsistent use of existing global tokens and presets.
- Duplicated values or patterns that should use existing shared definitions.
- Repeated structures that could be reusable components.
- Unnecessary one-off styling, redundant CSS or conflicting rules.
- Inconsistent spacing, typography, borders and responsive rules where there is evidence of accidental drift.
- Structural complexity or redundant markup that can be simplified safely.

Report findings and proposed changes before editing. Similar-looking values may be intentional; do not normalise them blindly.

**Mode B: deliberate visual redesign**

Only enter this mode when the user explicitly asks to redesign, refresh, modernise, restyle or otherwise change the visual direction. Establish the goal, audience and constraints, then propose or implement a deliberate visual system appropriate to the brief. Use the new-site showcase approval process when a new visual direction needs approval.

### Acceptance criteria

- The audit separates maintainability issues from subjective design preferences.
- Refactoring recommendations explain how intended appearance and behaviour will be preserved.
- Visual redesign is never silently inferred from a consistency audit.
- Changes are reversible, verified in the browser at desktop and mobile sizes, and documented.
- Existing Divi global tokens remain authoritative unless the user approves a change.

## 10. Priority 6: Visual regression testing

Treat this as a later capability, after the preceding workflows have stable test cases and clear expected behaviour.

First inspect the repository and available browser tooling. Prefer the simplest reliable repeatable process for capturing representative pages at fixed viewport sizes, comparing screenshots or other relevant rendered evidence, and reporting differences. Do not add a heavy framework without evidence that it is needed.

The workflow should distinguish genuine regressions from expected differences such as dynamic content, fonts loading, animation, timestamps or environment variation. Use representative pages and components rather than attempting to snapshot every possible state immediately.

### Acceptance criteria

- A documented, repeatable baseline and comparison process exists.
- Tests cover agreed desktop and mobile viewports.
- Reports identify meaningful visual changes and provide evidence for review.
- Known dynamic or noisy regions are handled deliberately rather than masking broad areas.
- The process can be run again after a Divi or dependency upgrade.

## 11. Skill and architecture approach

Use the six existing skills as the verified foundation:

- `divi5-core`
- `divi5-styling`
- `divi5-modules`
- `divi5-theme-builder`
- `divi5-dynamic`
- `divi5-presets`

Add focused skills where they create a clear, reusable capability. Candidate names are:

- `divi5-design-system`
- `divi5-page-production`
- `divi5-component-audit`
- `divi5-accessibility`
- `divi5-design-audit`
- A later visual regression skill or documented testing workflow, depending on the architecture that proves simplest.

These are proposed names, not a requirement to create six skills mechanically. Inspect the current skill format, naming conventions and skill-loading behaviour first. Combine closely related capabilities if that reduces complexity without making a skill unwieldy.

Keep central User Context concise and focused on mandatory behaviour, decision gates and skill routing. Put detailed procedures in the appropriate skills. Update existing core skills only where their current instructions need to support the new workflow.

New skills must:

- State when they should be invoked and what inputs they need.
- Load the existing skills relevant to their task.
- Define the procedure, decision points, verification and reporting format.
- Avoid duplicating module schemas and recipes already maintained elsewhere.
- Distinguish tested facts from documented or inferred knowledge.
- Remain within the existing free-Novamira and local-site constraints.

## 12. Suggested implementation order

Implement in small, testable increments. Reorder only if repository evidence gives a clear reason, and document the reason.

1. **Baseline and architecture review.** Confirm the current state, tests, skill conventions and documentation.
2. **New-site design approval workflow.** Establish the showcase gate and update central instructions. Test both the showcase-required and design-reference-supplied paths.
3. **Content-to-page production.** Implement both content modes and verify a representative page end to end.
4. **Component audit and proposed conversion.** Implement audit-only reporting first, then test the approval-gated conversion path.
5. **Accessibility checks.** Establish practical checks, reporting and remediation verification.
6. **Design-system audit/refactoring.** Implement appearance-preserving audit mode, then the separately invoked visual redesign mode.
7. **Visual regression.** Evaluate and implement once the core workflows have stable test cases.

At each stage, assess whether the capability belongs in an existing skill, a new skill, central User Context, a test helper or documentation. Prefer the smallest coherent implementation.

## 13. Testing and verification requirements

Use the project's established test approach and extend it where necessary. At minimum:

- Test new skill instructions with fresh agents where practical, not only the agent that wrote them.
- Use representative test briefs and pages, including edge cases and incomplete inputs.
- Test responsive rendering at the project's established desktop and phone widths.
- Verify every WordPress write by reading the saved data back.
- Inspect rendered output in a browser. Confirm the relevant page opens and behaves as intended.
- Check that the Visual Builder can open and edit authored pages where relevant to the workflow.
- Test approval gates explicitly, including confirming that downstream pages are not built before showcase approval.
- Test that audit-only modes make no writes.
- Test the distinction between verified outcomes and unverified claims in generated reports.
- Confirm new skills and their stored copies match according to the existing repository convention.
- Keep test pages and other temporary artefacts clearly identified. Clean up only artefacts created for the test and covered by the project's cleanup permissions.
- Record failures, fixes, remaining limitations and evidence labels in the appropriate test documentation.

Do not report a test as passed merely because a command exited successfully. Verify the outcome that matters to the user.

## 14. Documentation and change records

Keep project documentation aligned with the implementation.

- Update `docs/00-status-and-roadmap.md` with current status, completed work, outstanding work and the next useful step.
- Update `README.md` if the standard new-site setup or available capabilities change.
- Update `CLAUDE.md` and central User Context only for durable rules, mandatory approval gates, skill routing and safeguards. Keep implementation detail in skills.
- Create or update the relevant skill files and their stored copies.
- Update `docs/09-test-results.md` with test cases, environment/version details, results, evidence labels and limitations.
- Update `docs/08-site-change-log.md` for every WordPress write, including what changed, verification and undo steps.
- Update the relevant architecture, recommendation or decision documents if the implementation changes a previous decision. Do not create documentation churn for trivial changes.
- Preserve existing source attributions and licensing information in `NOTICE.md`.
- Do not put secrets or credentials in documentation, skills, logs or source control.

Keep claims version-specific where Divi behaviour may change. The local Divi source/docs may not match the installed version, so record version mismatches and verify uncertain behaviour against the active environment and official documentation when needed.

## 15. Definition of done

This expansion is complete when:

- The standard new-site workflow creates a design showcase and waits for approval when no approved design direction exists.
- Existing design references and explicit user instructions can bypass that gate appropriately.
- Content-to-page production supports both supplied-content and research-and-draft modes.
- Component audits report opportunities without making unapproved changes.
- Approved conversions are verified and reversible.
- Accessibility audits combine practical automation with browser/manual checks and report their limitations.
- Design-system refactoring preserves intended appearance by default, with deliberate redesign kept separate.
- Visual regression has either been implemented and tested to a useful standard, or is explicitly recorded as deferred with a reason and next step.
- Existing functionality remains intact and regression tests pass.
- All relevant skill copies and project documentation are synchronised.
- Every WordPress write has read-back verification and an undo record.
- The final report distinguishes implemented, tested, partially tested and deferred work, with evidence and any decisions still needed.

## 16. Final report format

At the end of the work, provide a concise report with:

1. **Implemented:** skills, workflow changes and supporting tools/files.
2. **Verified:** tests actually run, environment versions, browser checks and results.
3. **Not verified or deferred:** limitations and reasons.
4. **Approval needed:** any decisions or changes deliberately left for the user.
5. **Documentation updated:** key files.
6. **Rollback:** where to find undo instructions and any remaining risk.

Start by inspecting the repository and confirming the baseline. Then implement the priorities in order, preserving the existing working system and its safeguards.