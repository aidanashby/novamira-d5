# Changelog

## Unreleased

From the fresh-site install test (T30).

### Setup

- `CLAUDE.md` works on any site without editing. Personal rules go in a git-ignored `CLAUDE.local.md`.
- Changes to a user's site are logged in a git-ignored `site-logs/` folder, so `git pull` never conflicts.
- `novamira-setup` offers to activate Divi when it is installed but inactive, reports the Novamira version, and offers to move WordPress's default footer widgets and to allow Novamira calls without a prompt (risk stated first).
- `.gitattributes` keeps every file LF, so hashes match on Windows clones.

### Skills

- `divi5-styling`: Image border and radius on `image.decoration.border`; background image with a gradient overlay.
- `divi5-theme-builder`: works on a site where the Theme Builder was never used; layouts created with `et_theme_builder_insert_layout()`.
- `divi5-dynamic`: styling the post content module's text; ACF Link fields do not resolve; even card images with `et-pb-post-main-image`; check featured-image alt text before a loop.
- `divi5-modules`: accordion and toggle title heading levels.
- `divi5-core`: removing a child block safely; computed-style checks in the browser; ask the user to sign in to the browser, never create an admin link; a harmless WP Studio cache warning.

## 1.0.0 (21 October 2026)

First public release. Tested on Divi 5.16.0 (and 5.15.1), Novamira 1.12.11 (free, from 1.12.9), WordPress 7.1.3 and ACF 6.8.10, on a local WP Studio site. Evidence for every rule is in [docs/09](docs/09-test-results.md) (tests T0 to T29).

### Skills

- `divi5-core`: how Divi 5 stores a page, the `serialize_blocks()` build recipe, rows and columns, choosing modules, editing and copying pages, components, silent failures, and how to check the result.
- `divi5-styling`: backgrounds, gradients, video backgrounds, spacing, borders, shadows, sizing, fonts, hover, sticky, position, breakpoints, global colours and every variable type. Text alignment uses `orientation` from Divi 5.16.
- `divi5-modules`: attribute shapes for every registered module, contact forms, fullwidth sections, and the Menu module's collapsible mobile menu (Divi 5.16).
- `divi5-theme-builder`: Theme Builder storage, editing header, body and footer layouts, single and archive templates for a post type.
- `divi5-dynamic`: dynamic content, ACF fields, Loop Builder, pagination, filters by custom field and term, dates, custom post types, and moving date windows through the Date Conditions for Divi plugin.
- `divi5-presets`: reading and applying presets; proposing new ones and writing them after the user agrees.
- `divi5-design-system`: new-site and redesign workflow built around a design showcase draft the user approves first.
- `divi5-page-production`: draft pages from supplied content, with gaps and sources reported and no invented facts.

### Setup

- `novamira-setup` Claude Code skill: checks versions, uploads the skills from `skills/manifest.json`, writes the User Context after the user agrees, adds the WP Studio (SQLite) variables lock fix, verifies each copy and builds a smoke-test draft.
- User Context ([docs/user-context-proposed.md](docs/user-context-proposed.md)): drafts only, no deletion, presets and variables only after agreement, native modules only, and routing away from Novamira's built-in `novamira-design` skill.

### Documentation

- README for a public audience, including the Claude Code settings a clone does not bring (connection, permissions, auto mode, CLAUDE.md).
- MIT licence for this project's own content; NOTICE.md with the upstream divi5-skill licence.

### Known limits

- Not yet run on a fresh second site or on a real staging site.
- Instagram Feed, the post-content modules and Divi's Shortcode wrapper were not covered by the all-modules browser check.
