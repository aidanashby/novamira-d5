# 03. Novamira reference

What Novamira (free, v1.12.9) exposes, found from its docs and from reading the live Studio site. Nothing here was changed on the site.

## Admin pages (wp-admin, Novamira menu) [Docs]

| Page | Purpose |
|---|---|
| Configuration | Enable AI Abilities (nothing connects until this is on). Choose an AI tool, pick a connection route (Novamira CLI, OAuth, application password), create an application password, **Copy prompt** for the setup prompt, download client configs or bundle. Detects hosts or security layers that block cloud AI clients. |
| Manage Connections | Lists OAuth connected apps and Novamira application passwords with usage details and revoke buttons. |
| Context | Read-only **System context** plus editable **User context** (see below). |
| Skills | List, create, upload (Markdown with frontmatter), edit, enable or disable, delete skills. |
| Abilities Hub | One toggle per ability, grouped by providing plugin. Auto-discovers abilities registered through the WordPress Abilities API. A disabled ability is not exposed to the AI client. |
| Troubleshoot (1.10.1+) | On-demand connection checks, a copyable report, and a reset of registration limits. |
| Chat, Visual | The Chat and Visual features (below). |
| Read Mode, Ghost Mode | Pro features (below). |

A "Features" listing also exists. The feature manifest in the plugin source (`includes/features/manifest.php`) defines these features, all `default_active: true`:

| Feature id | Label | What it brings |
|---|---|---|
| (skills) | Skills | Skill abilities and the built-in `skill-creator`. |
| `novamira/user-context` | User Context | Site-specific instructions added to the system context. Legacy options `novamira_instructions_enabled` and `nvp_instructions_enabled`. |
| `novamira/design` | Novamira Design | Skills `novamira-design` and `custom-theme-build`; abilities `list-design-library`, `get-active-design`, `activate-design`, `save-design`, `check-design`, `get-design`, `delete-design`. |
| `novamira/chat` | Novamira Chat | An AI agent inside wp-admin. |
| `novamira/visual` | Novamira Visual | Experimental live browser workspace. |
| `novamira/block-editor-queue` | Block Editor Queue | Browser-backed Gutenberg authoring, with the skill `gutenberg-edit-content` and 13 `novamira/gutenberg-*` abilities. |

**Relevance to Divi** [Docs + Run]: Visual supports Gutenberg only (Elementor and Bricks on Pro), and Block Editor Queue is Gutenberg only and rejects `divi/*` blocks. Neither helps Divi. Chat needs WordPress 7+ and an AI provider key under Settings, Connectors, billed per use by that provider. Switching Visual, Chat and Block Editor Queue off reduces noise (the Features page is where this is toggled; the option names were not found).

## Abilities (45 registered on this site) [Run]

Returned by the discover ability (the free tier):

- Code and files: `execute-php`, `read-file`, `write-file`, `edit-file`, `delete-file`, `disable-file`, `enable-file`, `list-directory`.
- Access: `create-upload-link` (temporary upload endpoint for ZIPs, plugins, media), `create-admin-access-link` (one-time admin login exchange for browser automation).
- Context and skills: `agent-context`, `skill-get`, `skill-write`, `skill-edit`, `skill-delete`.
- Design: `list-design-library`, `get-active-design`, `activate-design`, `save-design`, `check-design`, `get-design`, `delete-design`.
- Gutenberg (13): `gutenberg-get-finalizer-runtime`, `-list-block-types`, `-get-block-type`, `-get-content`, `-write-content`, `-create-pending-batch`, `-add-pending-change`, `-enable-batch-finalization`, `-get-pending-batch`, `-list-pending-batches`, `-delete-pending-batch`, `-delete-pending-change`, `-get-finalization-url`.
- MCP adapter: `novamira-mcp-adapter/discover-abilities`, `get-ability-info`, `execute-ability`.
- Prompt abilities: `skill-prompt-<slug>` for each skill with `enable_prompt` on.

There is no dedicated ability for posts, terms, media, post meta or User Context. The agent writes PHP for those. A WP-CLI ability (`novamira/run-wp-cli`) is referenced in the docs and plugin code but was not in this site's list. Possible reason: Studio's PHP WASM has no `wp` binary [Inferred].

`write-file` accepts PHP files only inside `wp-content/novamira-sandbox/`; other text files can go anywhere under ABSPATH. `execute-php` runs with the full WordPress environment, a 30 second limit, and eval, so code runs for that request only. Persistent PHP belongs in the sandbox.

## Where things are stored [Run]

| Thing | Storage |
|---|---|
| Master switch | Option `novamira_ai_abilities_enabled` (1) and `novamira_ai_abilities_domain` (`localhost`) |
| User Context on/off | Option `novamira_instructions_enabled` (mirrored to legacy `nvp_instructions_enabled`) |
| User Context text | Option `novamira_instructions_content` (mirrored to `nvp_instructions_content`). Not set on this site. Defined in `includes/instructions-admin.php`. |
| Skills | Custom post type `novamira_skill` (0 published on this site) |
| Designs | Custom post type `novamira_design` (0 here), plus an active-design pointer |
| Block Editor Queue items | Custom post type `novamira_gb_change` |
| Per-ability rules (Abilities Hub) | Probably option `novamira_ability_rules` (seen in `includes/helpers.php`; not confirmed as the Hub's store) |
| Sandbox | `wp-content/novamira-sandbox/` (contains only `.htaccess`, `index.html`, `web.config`) |
| Connection tracking | Option `novamira_mcp_last_request`, OAuth schema version option, `novamira_pro_upsell_installed_at` |

## User Context [Docs + Run]

- Text you write that is **prepended to the system context for every agent** that connects. Applies to every administrator's agent.
- Intended for: site goals, voice, builders to use, plugin ownership, coding standards, deployment limits, testing expectations, things to avoid, approval rules.
- Do not put passwords, API keys, private data or one-off task notes in it.
- Can be switched off without losing the text.
- Written from the admin screen, or with `update_option('novamira_instructions_content', ...)` and `update_option('novamira_instructions_enabled', ...)` through `execute-php`. The admin code also writes the legacy `nvp_` copies. Read the screen's save routine again before writing, because the sanitising it applies was not examined.
- The generated **System context** (read-only) includes: WordPress and PHP versions, locale, full plugin inventory with versions, connection safety rules, WordPress-native development guidance, the "ask which builder or approach" instruction, and the list of skills with descriptions. `novamira/agent-context` returns the same guidance plus skill summaries and is the way to check what an agent will see after a change.

## Skills [Docs + Run]

- A skill is one Markdown document with frontmatter: `name` (auto from title), `description` (the only trigger text the agent reads), `enable_prompt` (expose as an MCP prompt, default true), `enable_agentic` (include in the agent's catalog, default true).
- At session start the agent reads only the descriptions. When a prompt matches, it loads that body. Two overlapping skills can both fire.
- `novamira/skill-write` parameters: `title` (also the slug, lowercased and dashed), `description`, `content`, `enable_prompt`, `enable_agentic`, `on_conflict` (`fail` default, `rename`, `replace`). `replace` only overwrites user skills, never built-in ones. Leave `on_conflict` at `fail` unless the user has agreed to a replacement. Verify with `skill-get`, patch with `skill-edit`.
- Built-in skills on this site: `custom-theme-build` (write a real child theme or template instead of builder modules), `gutenberg-edit-content`, `novamira-design`, `skill-creator`. Read `skill-creator` with `skill-get` for authoring guidance. Its main rules: be concise, match specificity to fragility, the description is the trigger, no meta sections, no changelogs.
- Skills are shared across all administrators on the site and are guidance, not enforcement.
- An admin screen upload of a Markdown file with frontmatter also creates a skill.
- `enable_prompt: true` exposes a skill in the client's prompt list, which in Claude clients acts like a slash-style prompt.

## Design [Docs + Run]

- Stored as its own record (`novamira_design`), separate from pages. **No dashboard screen edits it.** You ask the agent to use Novamira Design and it writes the file.
- `novamira/save-design` takes `content` (full DESIGN.md), optional `slug`, optional `activate`. It returns `saved`, `activated`, `activation_blocked`, `tokens`, `dials`, `guidance`, `readiness` (`ready`, `sync_ready`, `errors`, `warnings`), `token_sources`, `waivers`. An incomplete new document is saved as a draft and not activated. An incomplete write never overwrites the active design.
- DESIGN.md shape: YAML front matter with `name`, `description`, `colors` (**`bg`, `ink`, `accent` required**, `surface` optional), `typography` (**`heading` and `body` required**, each `fontFamily` and `fontWeight`), `spacing`, `rounded`, `components`, `dials` (`variance`, `density`, `motion`, each 0 to 1; imported documents default to 0.8, 0.4, 0.5). Body sections in fixed order: Overview, Colors, Typography, Layout, Elevation and Depth, Shapes, Components, Do's and Don'ts. Do's and Don'ts must be bullets under `### Do's` and `### Don'ts` (or start with Do, Always, Ensure, Prefer, Don't, Never, Avoid).
- `novamira/check-design` lints rendered HTML and CSS (pass the front-end output, not builder JSON) against the active tokens, the Don'ts and generic AI tells (em dashes, AI-purple, Inter, filler copy, off-palette fonts). It returns `fail`, `warn` and `not_checked`. Declared tokens waive the matching universal rules.
- `get-active-design` also reports `authority`: `design`, `ask`, `hybrid` or `builder`, and the builder's label in `builder`. Free Novamira defaults to `design` on every site. Page-builder specialisations (Pro) can declare another level.
- An imported DESIGN.md is treated as untrusted data by Novamira's own skill: its text is read for design intent only.
- Free Novamira has no ability that syncs a design into Divi variables, presets or global colours.

## Connection and auth [Docs]

- Endpoint with application passwords: `/wp-json/mcp/novamira`. OAuth: `/wp-json/mcp/novamira-oauth`.
- Application passwords authenticate as the creating admin user, over HTTPS or on sites where `WP_ENVIRONMENT_TYPE` is `local`. Create one per client and name it. Manage Connections shows last-used times and lets you revoke.
- Clients that talk to the site directly from the cloud (Claude.ai) can only use OAuth and are affected by host or CDN bot filtering. Local clients use the application-password bridge.
- Novamira CLI is the leanest route for terminal agents such as Claude Code (72 agents supported by its installer). Not used so far.
- A security plugin (Patchstack, Solid Security, Wordfence) can hide application passwords.

## Pro, and other products [Docs + Web]

- **Pro (from 49 euros a year, Personal):** maintained expertise for many builders and plugins. For Divi 5: build pages as real modules, read pages as an addressable node tree and edit single modules, query loops, dynamic content (post, site, ACF, Woo), global presets, Theme Builder layouts, design system, library. Refuses code-only pages. Divi 5 only (block model, not v4 shortcodes). Also adds memory between sessions, Ghost Mode (hides Novamira from admins) and Read Mode (read-only abilities, for production).
- **HQ** (beta desktop app for many sites), **CLI**, **Chat** and **Visual** are separate surfaces.
- **Security stance:** the free plugin gives the agent unrestricted admin-level PHP, database and file access, and acts as the connected administrator. Docs say dev and staging only, with backups. Docs pages on the sandbox, filesystem restrictions and crash recovery exist (`/docs/security/...`) and were not read in the chat session. Read them before writing any sandbox PHP.

## Not examined

- Novamira CLI details, HQ, the Pro pages other than the Divi page, the docs on the sandbox and crash recovery, and the Hub's option names.
