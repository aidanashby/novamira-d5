# novamira-d5

Verified Divi 5 skills that let Claude build and edit pages, Theme Builder templates and dynamic content on a WordPress site, through the free Novamira plugin. Built and tested on a local WP Studio site, October 2026. Private repo.

Current status, open items and roadmap: [docs/00-status-and-roadmap.md](docs/00-status-and-roadmap.md).

## What it is

[Novamira](https://novamira.ai/) is a WordPress plugin that runs an MCP server inside the site, so an agent can run PHP and manage files, skills and settings as the connected administrator. Its free tier has no Divi tooling: Divi 5 module abilities are in Novamira Pro.

This project closes that gap with knowledge rather than tooling. The agent writes Divi 5 content itself, following five Novamira skills that were checked rule by rule against Divi 5.15.1 on a real site:

| Skill | Covers |
|---|---|
| `divi5-core` | How Divi 5 stores a page, the build recipe, meta, rows and columns, choosing modules, editing and cloning pages, components, silent failures, and how to verify (read-back, render, browser) |
| `divi5-styling` | Backgrounds, gradients and video backgrounds, spacing, borders, shadows, sizing, fonts, hover, sticky, position, breakpoints, the site's global colours |
| `divi5-modules` | Attribute shapes for every registered module, contact forms, fullwidth sections |
| `divi5-theme-builder` | Theme Builder storage, editing header, body and footer layouts, creating single and archive templates for a post type |
| `divi5-dynamic` | Dynamic content, ACF fields, Loop Builder, pagination, dates, custom post types |

A short User Context (text in [docs/user-context-proposed.md](docs/user-context-proposed.md)) tells every agent on the site to use Divi only, build drafts, leave global settings alone and load `divi5-core`.

## How it works

- Divi 5 stores content as WordPress block markup. Agents build block arrays in PHP and serialise them with WordPress's own `serialize_blocks()`, which matches the Visual Builder's storage byte for byte. Parsing and re-serialising a builder page is lossless, so edits and copies are safe.
- Every rule in the skills was run on the site: written, rendered, read back and, where layout or JavaScript matters, checked in a browser at desktop and phone width. Pages written this way open, edit and save normally in the Visual Builder.
- Agents work on drafts and copies. Theme Builder edits, which go live at once, require the user's say-so.

## Results

- Fresh agents given only the skills built landing pages, FAQ copies, image and text rows, a styled contact page and a Movies grid, and answered read-only questions, after one round of fixes.
- Tested on the site: all registered modules, styling, cloning a published page, Theme Builder layouts and templates, loops, ACF fields and pagination.
- Decision: no Novamira Pro. Building pages as real modules, single-module edits, Loop Builder, dynamic content and Theme Builder all worked on the free plugin. Preset and design-system writes were not tested (docs/10).

## Using it on another site

1. Install Novamira on a development or staging copy, never a live site. Novamira gives the agent full admin, PHP, database and file access.
2. Connect Claude (Claude Desktop extension or Claude Code; see CLAUDE.md, "Connecting Claude Code").
3. Create the five skills from `skills/` with `novamira/skill-write` and the User Context from docs/user-context-proposed.md. Skills must contain no backslashes; check each stored copy by md5.
4. Check the site's Divi version. The skills were verified on 5.15.1; rerun the key tests (docs/09) after a Divi upgrade.

## Repository layout

| Path | Contents |
|---|---|
| `CLAUDE.md` | Rules for Claude in this project: local site only, standing approvals, gates |
| `skills/` | Source of the five Novamira skills (identical to the copies stored on the site) |
| `docs/00-status-and-roadmap.md` | Status, open items, roadmap, learnings, how to resume |
| `docs/01` to `docs/04` | Environment, session log, Novamira reference, audit of `divilovewp/divi5-skill` |
| `docs/05-divi5-authoring-notes.md` | Divi 5 rules with a status table showing what the tests confirmed or corrected |
| `docs/06-recommendations-and-plan.md` | The original plan and test design |
| `docs/07-sources.md` | Links |
| `docs/08-site-change-log.md` | Every write to the site, with how to undo it |
| `docs/09-test-results.md` | Every test and its evidence |
| `docs/10-decision-memo.md` | Free versus Pro, design tokens, decisions |
| `NOTICE.md` | Third-party licences (divi5-skill is MIT) |

## Evidence labels

Used in the docs: [Run] observed on the Studio site; [Docs] from Novamira's or the local Divi documentation; [Web] from a web page listed in docs/07; [Skill] a divi5-skill claim not yet verified; [Inferred] reasoning, not verified.

## Credits

Parts of the skills are adapted from [divilovewp/divi5-skill](https://github.com/divilovewp/divi5-skill) (MIT, copyright 2026 Shashank Gupta, divilove.com), then verified and rewritten for Divi 5.15.1. See NOTICE.md.
