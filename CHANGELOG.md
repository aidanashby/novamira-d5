# Changelog

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
