# Divi 5 skills for Novamira

Let Claude build and edit Divi 5 pages for you on a WordPress development or staging site. You ask in plain English ("make a landing page for our spring appeal", "add an FAQ to the volunteering page", "show our events in a grid"), and Claude builds it as a draft with real Divi modules that you can open and edit in the Visual Builder as usual.

This repository holds eight skills and a short set of site instructions for the free [Novamira](https://novamira.ai/) WordPress plugin, plus a setup routine that installs them on a site. Every rule in the skills was tested on a real Divi 5 site.

## What you can ask for

- New pages and sections, built from Divi's own modules (headings, text, buttons, images, blurbs, accordions, tabs, sliders, forms and the rest), laid out in rows that stack on phones.
- A new look for a site: Claude builds a design showcase page first and waits for your approval before building anything else.
- Pages built from content you supply (a brief, a document, pasted copy), with gaps and contradictions flagged instead of filled with invented facts.
- Styling: colours, spacing, borders, shadows, fonts, hover effects, sticky elements and responsive settings, using your site's global colours, variables and presets where they exist.
- Theme Builder work: headers, footers and templates for custom post types.
- Dynamic content: lists and grids of posts or custom post types with the Loop Builder, ACF fields, filters and pagination.
- Edits to an existing page, or a safe copy of it.

## What Claude will not do

The site instructions installed with the skills tell Claude to:

- build new pages as drafts, and publish only when you ask;
- never delete, bin or unpublish a page, or overwrite one unless you ask;
- propose new presets, global colours and variables, and create them only after you agree;
- use native Divi modules, never a Code module or injected HTML, CSS or JavaScript for layout.

## Before you start

You need:

- WordPress with **Divi 5.7.0 or later** (tested on 5.15.1 and 5.16.0). Divi 4 is not supported.
- The **Novamira** plugin (free version, tested on 1.12.9 to 1.12.11).
- **Claude Code**, connected to the site through Novamira.
- A **development or staging copy** of the site. Never use this on a live site: Novamira gives the connected agent full administrator, PHP, database and file access.

## Set up a site

1. Install and activate Novamira on the development or staging site.
2. In wp-admin go to **Novamira, Configuration** (on some versions **Novamira, Connect**), choose **Claude Code**, create an application password, and click **Copy prompt**.
3. Start Claude Code in a clone of this repository and paste the prompt. Claude writes the connection settings and asks you to restart the session. Keep the prompt private: it contains the password.
4. After the restart, ask Claude: "Set up this site for Divi 5." The `novamira-setup` skill then:
   - checks the Divi and Novamira versions and stops if something essential is missing;
   - uploads every skill in `skills/manifest.json` and checks each copy byte for byte;
   - shows you the site instructions and writes them once you agree;
   - adds a small database lock fix on WP Studio (SQLite) sites, so Divi can save global variables;
   - builds one test draft, checks it, and asks you to open it in the Visual Builder.

If you connect through the Claude Desktop extension instead, start the site first. If the Novamira tools are missing from a session, switch the extension off and on in the session's connector menu.

## Use it

Ask for what you want in plain English. Claude reads the site instructions, loads the skills it needs and builds a draft. When it finishes it tells you the draft's title and link, what it checked (stored content, rendered output and, where it has a browser, desktop and phone views), and anything it could not do or needs from you.

Open the draft in the Visual Builder to review and edit it, then publish it yourself or ask Claude to.

After a Divi or Novamira update, read their changelogs and rerun the tests in [docs/09](docs/09-test-results.md) that the changes touch. Test T23 shows how.

## The skills

| Skill | Covers |
|---|---|
| `divi5-core` | How Divi 5 stores a page, the build recipe, rows and columns, choosing modules, editing and copying pages, components, silent failures, and how to check the result |
| `divi5-styling` | Backgrounds, gradients and video backgrounds, spacing, borders, shadows, sizing, fonts, hover, sticky, position, breakpoints, and global colours and variables of every type |
| `divi5-modules` | Attribute shapes for every registered module, contact forms, fullwidth sections, the Menu module's collapsible mobile menu |
| `divi5-theme-builder` | Theme Builder storage, editing header, body and footer layouts, creating single and archive templates for a post type |
| `divi5-dynamic` | Dynamic content, ACF fields, Loop Builder, pagination, filters by custom field and term, dates, custom post types |
| `divi5-presets` | Reading and applying module and option-group presets; proposing new or changed ones and writing them after you agree |
| `divi5-design-system` | New-site and redesign workflow: a design showcase draft that you approve before other pages are built |
| `divi5-page-production` | Building a draft page from supplied content, reporting gaps and sources without inventing facts |

The site instructions (User Context) are in [docs/user-context-proposed.md](docs/user-context-proposed.md).

## How it works

Novamira runs an MCP server inside WordPress, so Claude can run PHP on the site as the connected administrator. Its free version has no Divi tools, so this project supplies knowledge instead: skills that tell Claude exactly how Divi 5 stores pages.

Divi 5 stores content as WordPress block markup. Claude builds each page as PHP arrays and turns them into markup with WordPress's own `serialize_blocks()`, which produces exactly what the Visual Builder saves. Reading a page and writing it back loses nothing, so edits and copies are safe.

Every rule in the skills was written to the test site, rendered, read back and, where layout or JavaScript matters, checked in a browser at desktop and phone width. Fresh Claude agents given only the skills then built pages, layouts and loops from short requests. The evidence is in [docs/09](docs/09-test-results.md).

## Known limits

- Divi's own loop filters take fixed dates, so "events from today onwards" needs the free [Date Conditions for Divi](https://github.com/aidanashby/date-conditions-for-divi) plugin. The skills know how to use it, but that part has not yet been tested on a site.
- Some modules need data from outside the page (a map API key, a Mailchimp list, a payment account) before they show anything.
- Claude reads and places Divi components (reusable linked elements), but writes to them only when you ask.

## Repository layout

| Path | Contents |
|---|---|
| `skills/` | The eight Novamira skills, and `manifest.json` listing them for setup |
| `.claude/skills/novamira-setup/` | The Claude Code skill that installs everything on a site |
| `tools/studio-divi-variables-lock.php` | The lock fix for WP Studio (SQLite) sites |
| `docs/00-status-and-roadmap.md` | Status, open items, roadmap, learnings, how to resume |
| `docs/01` to `docs/07` | Background: environment, session log, Novamira reference, audit of `divilovewp/divi5-skill`, Divi 5 authoring notes, the original plan, sources |
| `docs/08-site-change-log.md` | Every write made to the test site, with how to undo it |
| `docs/09-test-results.md` | Every test and its evidence |
| `docs/10-decision-memo.md` | Free versus Pro, design tokens |
| `docs/11-design-and-content-workflow-expansion.md` | Design for the design-system and page-production skills |
| `CLAUDE.md` | Working rules for Claude on the original test site |
| `NOTICE.md` | Third-party licences |

Evidence labels used in the docs: [Run] observed on the test site; [Docs] from Novamira's or Divi's documentation; [Web] from a page listed in docs/07; [Skill] an unverified claim from divi5-skill; [Inferred] reasoning, not tested.

## Credits

Parts of the skills are adapted from [divilovewp/divi5-skill](https://github.com/divilovewp/divi5-skill) (MIT, copyright 2026 Shashank Gupta, divilove.com), then verified and rewritten for Divi 5. See [NOTICE.md](NOTICE.md).
