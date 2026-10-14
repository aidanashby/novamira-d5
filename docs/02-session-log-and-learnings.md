# 02. Session log and learnings

Chronological summary of the chat session (14 October 2026). Claude Desktop with web search, a filesystem MCP and the Novamira connection.

## 1. Can Novamira free, Claude and Divi work with WP Studio?

Answer given: yes in principle, with conditions. [Web] Free Novamira needs WordPress 6.9+ and PHP 8.0, works at the PHP level with any builder, and is dev and staging only. Divi 5 module tooling (build as real modules, edit single modules, loops, presets, Theme Builder) is Novamira Pro only, from 49 euros a year (Personal), 129 (Agency), 249 lifetime (Agency). Pro refuses code-only pages. Application passwords need HTTPS or `WP_ENVIRONMENT_TYPE` set to `local`. Studio supports custom `.local` domains with HTTPS since 1.3.9. Risks raised: Studio's SQLite database and WP-CLI under PHP WASM. [Run] The SQLite database was confirmed later. WP-CLI was not tested.

A remote host (for example staging on Krystal) is also supported: the plugin runs on the server and the client connects over HTTPS with an application password. Hosts and security plugins can block REST or the Authorization header.

## 2. Connecting Claude Desktop (what broke)

- **OAuth via `mcp-remote` failed.** Config entry used `npx -y mcp-remote http://localhost:8881/wp-json/mcp/novamira-oauth`. The browser consent page opened, then on Authorize: "Novamira could not create the OAuth signing keys for this site. The PHP error log records the OpenSSL reason. Run `wp novamira oauth-keys generate` from WP-CLI". [Run] PHP reports the `openssl` extension as loaded, so the cause is probably OpenSSL configuration inside Studio's PHP WASM [Inferred]. The suggested fix `studio wp --path "<Studio sites folder>\divi-5" novamira oauth-keys generate` was never run. [Docs] Novamira's changelog says the command exists to create keys "from a working PHP environment", so it may fail the same way on Studio.
- **Decision:** use the application password route instead. [Docs] Novamira describes OAuth as mainly for cloud clients and recommends the application password route (local bridge) for clients on the user's machine. The OAuth config entry was reverted.
- **The `.mcpb` bundle install.** Double-click and drag-and-drop did not work on the user's Claude Desktop (no default app for the file type, and dragging into the window attached it to the chat). What worked: Settings, Extensions, Advanced settings, Install Extension..., select the file. Then fully quit and reopen Claude Desktop. Anthropic's docs list all three methods but the Settings route is the reliable one here. The bundle needs Claude Desktop 1.24012.1 or later.
- Manual alternative (not used): `@automattic/mcp-wordpress-remote@latest` via `npx` with env `WP_API_URL` (`http://localhost:8881/wp-json/mcp/novamira`, no `-oauth` suffix), `WP_API_USERNAME`, `WP_API_PASSWORD`. On Windows with a TLS-inspecting proxy add `NODE_OPTIONS=--use-system-ca`.

## 3. First live checks [Run]

Discover, get-ability-info and `execute-php` all worked. Results are in docs/01. `execute-php` ran in about 3 to 18 ms for simple reads; one source scan of the plugin took about 9 seconds.

## 4. Audit of divilovewp/divi5-skill

See docs/04. Summary: legitimate and useful as source material, but about two months behind Divi 5.15.1, with a funnel to the author's paid plugin (Divi Connect). The user chose "Novamira skill" as the target format.

## 5. Novamira configuration research

See docs/03. Key finding: User Context and skills can be written through PHP, designs through the `save-design` ability, and a sandbox helper file can hold reusable PHP. Nothing was written.

## Learnings worth keeping

1. **Novamira's system context makes the agent ask questions.** It tells the agent to check installed page builders and ask the user which approach to use (page builder, Gutenberg, classic templates, child theme, custom theme) before building. It also tells the agent to load the `novamira-design` skill before any visual work. A short User Context can answer the first once.
2. **The built-in `novamira-design` skill can trigger a write.** If no design is active, it tells the agent to capture the site's existing look as a DESIGN.md and `save-design` with `activate: true` before building, unless the builder owns the design system. On free Novamira the default authority is `design` on every site, builder or not. Add a User Context line telling the agent not to create or activate a Novamira design unless asked (docs/06).
3. **Novamira's design skill says tokens must be materialised in the builder.** A token that exists only in DESIGN.md is invisible to Divi. It also says not to hand-roll writes to site globals when no sync ability exists. Free Novamira has no Divi sync ability.
4. **Gutenberg abilities reject `divi/*` blocks.** `gutenberg-write-content` and `gutenberg-add-pending-change` say builder-owned namespaces must be written with the builder's own abilities (Pro). On free, Divi pages are written directly with PHP.
5. **Skills are flat Markdown.** One document each, 1 MB hard limit, advised under 5,000 words. Everything loads at once, so prefer several sharply described skills over one large one.
6. **Fetch limits.** GitHub's API and some pages blocked the fetch tool. `raw.githubusercontent.com` worked for file contents.
7. **Corrections made during the session.** The first advice to install the bundle by double-clicking was wrong for this machine (see above). A first reading of a Novamira docs page placed the prompt under "Connect"; current docs place it under "Configuration". Both names appear in the docs, so check the live admin.
8. **Studio specifics from the earlier review** (Divi 5.14.0): Studio restricts PHP to the site folder, which makes Divi's `d5-readiness` check print warnings into REST responses. This will not happen on Krystal. Studio has no LiteSpeed, so cache behaviour needs separate testing on staging.

## Tool names, for reference

In the chat session Novamira abilities were called as `novamira/<ability>` through the `execute-ability` tool. Useful ones: `novamira/execute-php`, `novamira/skill-get`, `novamira/skill-write`, `novamira/skill-edit`, `novamira/agent-context`, `novamira/list-design-library`, `novamira/get-active-design`, `novamira/save-design`, `novamira/check-design`.
