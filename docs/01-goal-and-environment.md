# 01. Goal and environment

## Background

The user (Aidan) runs several WordPress sites on Divi 5 and wants Claude to build and edit Divi 5 pages programmatically, on those sites and on a rebuild of his own freelance site (Lucid Rhino Web Design). Constraints he has stated: Divi 5 only (not Divi 4), reseller hosting at Krystal rather than a VPS, and interest in what is free versus paid and whether documentation is enough to build his own tooling. He has already evaluated other Divi 5 AI tooling (DiviOps, Respira and others) and a prior independent review of `doughoseck/divi5-builder` exists locally (see below). A previous finding of his: content injected through the WordPress REST API into Divi 5 pages can appear blank in the Visual Builder. This project tests whether Novamira's route (PHP running inside WordPress) avoids that.

Novamira is the free, open-source MCP plugin from Dynamic.ooo. Its free tier gives an agent PHP execution, file, database and skill/design abilities. Divi 5 module-level tooling is Novamira Pro only.

## The test site [Run, 2026-10-14]

| Item | Value |
|---|---|
| Site | WP Studio site named `divi-5`, folder `<Studio sites folder>\divi-5` |
| URL | `http://localhost:8881` |
| WordPress | 7.1.3 |
| PHP | 8.3.33 |
| Database | SQLite via `WP_SQLite_DB` (`8.0.38-mysql-on-sqlite-3.0.2`). MySQL-specific SQL may fail. |
| Environment type | `local` |
| Theme | Divi 5.15.1 (`ET_BUILDER_VERSION` is `5.15.1`, Divi 5 builder active, no parent theme) |
| Novamira | 1.12.9, free. `novamira_ai_abilities_enabled` is 1. |
| Active plugins | Advanced Custom Fields 6.8.10, Date Conditions for Divi 0.1.4, Moondream Alt Text Generator 1.1.8, Novamira 1.12.9, Plugin Check 2.1.0, Reading Progress 0.1.2, Responsive Goodies 0.5.2, Shopping List 0.11.0, Strong Password Generator 1.0.2, UC Calculator 1.3.1, WP TOC Block 0.1.1 |
| Divi blocks registered | 88 `divi/*` block types. Includes `divi/global-slot` and `divi/layout`. Does not include `divi/global-layout`. Gravity Forms, Imagely and Woo modules are absent because those plugins are not installed. |
| Custom breakpoints | Option `et_divi_builder_breakpoints` is unset, so defaults apply (Desktop, Tablet, Phone on). |
| Divi/builder postmeta keys present | Only `_et_pb_page_layout` and `_et_pb_use_builder` (no builder-saved Divi 5 page has existed to show the full set). |
| Novamira abilities registered | 45 (WordPress Abilities API) |
| Novamira user data | 0 user skills, no design saved or active, no User Context saved, sandbox folder empty |

## The connection [Run]

- Claude Desktop is connected through the Novamira MCP bundle (`.mcpb`) with an application password. Tools appear as `mcp__claude-device__lcl-Novamira_Divi_5-mcp-adapter-*` in the chat session. The connected WordPress user is ID 1.
- The Claude Desktop config file (`claude_desktop_config.json`) holds only the pre-existing `notebooklm` server. An OAuth entry that was tried and failed was reverted (see docs/02).
- Novamira's own operating rules, returned by its discover ability: never update, replace, deactivate or delete the Novamira plugin, and never revoke the Novamira OAuth connections or application passwords of user ID 1, because that would cut the connection in use.

## Local resources on the user's machine

| Path | What it is |
|---|---|
| `<projects folder>\divi5-docs\Divi 5 Documentation` | Local mirror of Divi 5 help articles (about 300 Markdown files). `Developer\` holds developer docs, with `Release Notes\` only up to 5.11.1. `divi-5-update-status.md` (fetched 2026-10-12) lists changes up to 5.15.1. |
| `<projects folder>\divi5-builder\Divi` | Copy of the Divi theme source (5.14.0 at the time of the earlier review). Use it to read Divi's own module metadata and PHP. A search for `Heading\module.json` in the first level found nothing, so the module metadata lives under another path. Locate it before relying on it. |
| `<projects folder>\divi5-builder\divi5-builder-review.md` | Independent review (2 October 2026) of `doughoseck/divi5-builder`, with hands-on findings on Divi 5.14.0 in WP Studio. Relevant measured facts are summarised in docs/05. |
| `<projects folder>\divi5-builder\repo` | Copy of that repo. |
| `<projects folder>\CLAUDE.md` | Imports `claude-skills\claude-projects.md` (working principles that apply here). |
| `<projects folder>\date-conditions-for-divi`, `responsive-goodies` and others | The user's own plugins, several of which are active on the Studio site. |

## Other Divi tooling the user has looked at

`divi5-builder-review.md` compares: Elegant Themes' own Divi AI Agent (shipped in Divi 5.12, 4 September 2026; official MCP announced, no date), DiviOps, Wiplink, Respira, D5MCP and `doughoseck/divi5-builder`. Its verdict for divi5-builder was "fork and trim for staging-only drafting". The Novamira route in this project is a different mechanism (PHP inside WordPress, no custom REST plugin), so that review's REST, mu-plugin and credentials-file findings do not apply directly. Its measured Divi behaviour does apply (see docs/05).
