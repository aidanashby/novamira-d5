Written for Divi 5.15.1 (15 October 2026). Load `divi5-core` first, and the `site-design` skill if it exists. Use `divi5-styling`, `divi5-modules`, `divi5-presets` and `divi5-dynamic` for construction. This skill turns content into a page; the other skills say how to build it.

## When to use

The user supplies content for a page: a page spec (information architecture and copy), a document, notes, structured data or copy pasted in chat, and asks for a page built from it.

- No visual direction set on this site (no `site-design` skill, no design references): load `divi5-design-system` first.
- This skill does not research or write new facts. When content is missing, list the gap; do not fill it.

## Step 1: write a page spec

Before building, write a short spec from what the user gave you. If they supplied a spec, check it against this list instead.

- Purpose: what the page is for, in one sentence.
- Audience: who reads it and what they arrive wanting.
- Main message and the one main action (join, donate, book, contact), plus any secondary actions.
- Content inventory: every section, fact, figure, name, date, link, image and form in the source, in source order.
- Calls to action with their link targets.
- Page title, slug and meta description if supplied.
- Constraints: brand rules from `site-design`, words to use or avoid, accessibility needs.

## Step 2: find gaps

Sort each problem into one of two kinds.

- **Blocking:** a missing fact the page cannot work without (a date, price, address, link target, eligibility rule), or two sources that contradict each other. Do not guess. Use a visible placeholder in the page, such as `[placeholder: opening hours]`, and list it in the report. If the page would be misleading without the fact, ask one question before building.
- **Editorial:** headings, ordering, short link labels, splitting a long paragraph, button wording that the source implies. Decide these yourself and list them in the report.

Never drop source content silently. If something does not fit the page, say what and why in the report.

## Step 3: plan the structure from the content

1. Order the sections by what the reader needs first, ending at the main action. Keep the source's meaning; reordering is allowed, rewording facts is not.
2. Choose a pattern per section from what the content is: a list of steps (numbered blurbs or a list), comparable options (cards), a single strong message (call to action), questions (accordion), people (team blurbs or testimonials), figures (number counters only when the figure is sourced).
3. Reuse patterns approved in `site-design` and the showcase page. Do not add a pattern the showcase did not show without saying so.
4. Styles: read the site's presets, global colours and variables first and use those that fit. When the same style repeats (for example three identical buttons or cards) and no preset fits, style them locally and propose a preset in the report (`divi5-presets`); do not write it.
5. One `h1` (the page heading). Section headings `h2`, sub-headings `h3`, in order without skipping levels.

## Step 4: build and check

1. Build the draft with `divi5-core`. Title it as the spec says.
2. Verify as `divi5-core` says: read-back, render, browser at desktop and 375px.
3. Then check: every inventory item appears on the page; every link target works or is listed as a placeholder; every image has meaningful `alt` text; the heading outline has one `h1` and no skipped levels; button and link text makes sense out of context (no bare "Read more"). Use `divi5-accessibility` for a fuller check when asked.

## Step 5: report

- Page ID, title, status (draft), preview link.
- Sources used (which file, message or spec).
- Blocking gaps and contradictions, each with where its placeholder sits.
- Editorial decisions you made.
- Presets, colours and variables used, and any you propose.
- Content left out, and why.
- Checks run and passed, checks not run, and anything only the user can check (facts, final copy, publishing).
