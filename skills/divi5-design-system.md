Written for Divi 5.15.1 (15 October 2026). Load `divi5-core` and `divi5-styling` first; use `divi5-modules` and `divi5-presets` as needed. This skill decides the visual direction; the other skills build it.

## When to use

- The user asks for a new website, a new set of pages, or a redesign, refresh or restyle.
- The user asks what the site should look like, or for a design direction.
- Not for a single page on a site whose direction is already set: then load the `site-design` skill if it exists, and build with `divi5-page-production`.

## Step 1: is the visual direction already set?

The direction is set when any of these exist. Then skip the showcase and follow it.

- A `site-design` Novamira skill on this site (approved decisions from an earlier showcase). Load it.
- Design references the user supplied: mock-ups, screenshots, a site to reproduce, a brand guide with colours and fonts.
- A specific design brief the user calls approved.
- The user says to skip the showcase.

If none exists, the direction is not set. Build a showcase (step 2) and stop. Do not build any other content page until the user approves it, even if the brief asks for several pages. Setup that does not depend on the look (menus, page stubs left empty, reading the content) may go ahead.

## Step 2: build the showcase draft

One draft page titled `Design showcase: <site or project name>`. It shows the visual system through real-looking sample content for this site. Pick what this site will use; do not include every module.

1. Read the brief: audience, purpose, main actions, tone. Read the site's global colours, variables and presets (`divi5-styling`, `divi5-presets`).
2. Decide the system and write it down before building: colour roles (background, surface, text, accent, call to action), heading and body fonts with a size scale, spacing scale (section and row padding), corner radius, button style, image treatment.
3. Use global colours and existing presets where they fit. When the brief needs colours the globals do not have, use plain values on the showcase and list them as proposed global colours. List repeated styles (buttons, cards, headings) as proposed presets. Write neither until the user approves.
4. Sections, in order, each with a small label heading such as "Typography":
   - Typography: H1 to H4, body copy, a link, a list, a quote.
   - Buttons and links: primary and secondary buttons, hover states.
   - A hero treatment. Add a second only when there is a real choice to make, and label them "Option A" and "Option B".
   - Two or three row structures the site will need (for example image and text, three cards, a two-thirds and one-third split).
   - Content patterns from the brief: statistics, testimonials, calls to action, notices, a form, an accordion, a team grid. Only those the site will use.
   - Colour and background use: light, tinted, dark and image sections, each with text on it.
5. Set the font on every text part, not only headings and body: buttons take their own font (`button.decoration.font.font`) and fall back to the theme font, so set it on each button. Cards and calls to action likewise.
6. The showcase is judged on how it looks, so make it look like a page of this site, not a style sheet: give cards a surface (background, border or shadow), use at least one image and text row, and keep section labels small.
7. Write sample copy that fits the brief. Mark invented figures and names as samples (for example "[sample]"), so nobody mistakes them for content.
8. Use the media library for images; meaningful `alt` text on each. If nothing fits, use Divi's placeholder image and say so; do not leave the image row out.

## Step 3: check, then stop and ask

1. Verify as `divi5-core` says: read-back, render, then a browser at desktop and phone width (375px). Check text contrast on every background.
2. Report: page ID and preview link, the system you chose (colour roles with hex values, fonts, scale), proposed global colours or presets, and anything you could not check.
3. Ask the user to review it at desktop and phone width and approve or ask for changes. Approval covers the look and the patterns shown, not the sample copy, accessibility compliance or publishing.
4. Stop. Build no other content page until the user approves.

## Step 4: after approval

1. Write the approved decisions to a Novamira user skill named `site-design` with `novamira/skill-write` (no backslashes): the showcase page ID, colour roles and values, fonts and scale, spacing, button and image style, the patterns approved and the options rejected. Later pages follow it.
2. If the user approved proposed global colours or presets, write them only now, with `divi5-presets` for presets, and read them back.
3. Leave the showcase as a draft. It is the reference page; do not publish or delete it.

## Redesign

Only when the user asks to redesign, refresh, modernise or restyle. An audit or clean-up request is never a redesign.

1. Agree the goal, audience and what must stay (brand colours, logo, content).
2. Build a new showcase as in step 2, titled `Design showcase: <name> (redesign)`, with the current look as one option when the user wants a comparison.
3. Stop for approval. Then update `site-design`, and change existing pages only by draft copies (`divi5-core`, clone a published page).
