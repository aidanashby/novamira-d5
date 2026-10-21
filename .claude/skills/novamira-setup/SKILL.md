---
name: novamira-setup
description: Install this repo's Divi 5 Novamira skills and User Context on a WordPress site that Claude Code has just been connected to through Novamira (application password). Checks Divi and Novamira, uploads every skill in skills/manifest.json, writes the User Context, adds the SQLite lock fix where needed, verifies each copy and runs a smoke test. Use when the user says they have connected a new site, or asks to set up, install or update the Novamira skills on a site.
---

# Set up a site for Divi 5 work through Novamira

Run from the `novamira-d5` repo. The site's Novamira MCP tools must already be connected (`/mcp` shows the server). Call abilities through that server's `execute-ability` tool.

## 0. Which site, and is it allowed

1. Call `novamira/execute-php` with `return [home_url(), wp_get_environment_type(), get_bloginfo('name')];`.
2. Name the URL and environment to the user and ask with the choice tool whether this is the site to set up. Novamira gives full admin, PHP and file access, so never set it up on a production site (environment `production`, or a live domain); a `local` or `development` site needs the user's yes, and a staging site their explicit yes after naming the risk. If the project's CLAUDE.md names a different site, tell the user it needs adapting (README, "Settings that live outside this repository").
3. If several Novamira servers are connected, confirm with the user which one is this site before writing anything.

## 1. Checks, and what each result means

Run one read-only PHP call:

```php
global $wpdb;
return [
  'divi'      => defined('ET_BUILDER_VERSION') ? ET_BUILDER_VERSION : null,
  'theme'     => wp_get_theme()->get_template(),
  'abilities' => array_values(array_filter(array_keys(wp_get_abilities()), fn($n) => str_starts_with($n, 'novamira/'))),
  'db'        => defined('DB_ENGINE') ? DB_ENGINE : 'mysql',
  'skills'    => wp_list_pluck(get_posts(['post_type' => 'novamira_skill', 'numberposts' => -1, 'post_status' => 'any']), 'post_name'),
  'context'   => get_option('novamira_instructions_content'),
];
```

| Finding | Action |
|---|---|
| `divi` null, or the active theme is not Divi | Stop. The skills are Divi 5 only. |
| `divi` below 5.0 | Stop. Divi 4 stores pages differently. |
| `divi` 5.x below 5.7.0 | Warn the user: 5.7.0 is the earliest version with every feature the skills use (docs/00, "Setup skill: version rules"). Continue, then run the T1 round-trip check in step 5 before any page work. |
| `divi` 5.7.0 or later | Continue. No warning. |
| Any of `novamira/execute-php`, `novamira/skill-write`, `novamira/skill-edit`, `novamira/skill-get`, `novamira/agent-context` missing | Stop and name the missing ability; the Novamira version or its settings differ from the tested 1.12.9 to 1.12.11. |
| `db` is `sqlite` (WP Studio) | Install the lock fix in step 3. |
| A skill named in the manifest already exists | Go on, but in step 2 compare first and ask before replacing a copy that differs from the repo. |
| `context` not empty and differs from the repo text | Show the user both and ask before replacing (gate c). |

## 2. Upload the skills

For each entry in `skills/manifest.json`, in order:

1. Read the file from `skills/`. It must contain no backslash (Novamira's skill save strips them); if one is found, stop and fix the file first.
2. If the skill does not exist: `novamira/skill-write` with `title`, `description` and `content` from the manifest and file, `enable_prompt: true`, `enable_agentic: true`, `on_conflict: "fail"`.
3. If it exists and differs: ask the user, then `novamira/skill-edit` with `slug`, `description` and `content`.
4. Verify: `novamira/execute-php` returning `md5(get_post_field('post_content', <id>))` and the excerpt, and compare with the local file's md5 (`md5sum`, LF line endings) and the manifest description. Every skill must match.

## 3. Lock fix (SQLite only)

If step 1 found `sqlite`, ask the user (it writes a file outside Novamira's sandbox), then write `tools/studio-divi-variables-lock.php` to `wp-content/mu-plugins/` with `execute-php` (`file_put_contents`, refusing if the file exists). Verify on a new call that `get_mu_plugins()` lists it and `has_filter('et_builder_5_global_variables_got_lock')` is true. Without it, Divi's Variable Manager cannot save on WP Studio.

## 4. User Context

1. Take the text in the code block under "Current text" in `docs/user-context-proposed.md`. Show it to the user once and get approval (gate c), unless the site already holds exactly this text.
2. Write it with `execute-php`: `$f = implode(chr(92), ['', 'Novamira', 'Context', 'instructions_update_content']); $f($text);`
3. Verify that `get_option('novamira_instructions_content')` and `get_option('nvp_instructions_content')` both equal the text, then call `novamira/agent-context` and confirm the text appears in its instructions, including the sentence that excludes `novamira-design` (Novamira 1.12.11's system context tells agents to load it; T24). If the text is missing, the User Context feature is switched off: from 1.12.11 it is the feature `novamira/user-context` (on by default, the old `novamira_instructions_enabled` option is legacy), so ask the user to switch it on in Novamira's settings.

## 5. Smoke test

1. Build one draft page titled `Setup check` with `divi5-core`'s recipe (a heading, text and button in one row), read it back (`same` must be true) and render it (`the_content` contains the heading text).
2. If Divi was below 5.7.0, also parse and serialise an existing builder-saved page and confirm the output equals the input (T1); if it does not, tell the user the skills are not safe on this version.
3. Ask the user to open the draft in the Visual Builder and confirm it edits. Move the draft to the bin afterwards (only this draft).

## 6. Report and log

- Report: site URL, Divi and Novamira versions, warnings, skills installed or updated (all md5-matched), User Context state, lock fix, smoke test result, anything left for the user.
- For the Studio site, add each write to `docs/08-site-change-log.md` with its undo. Never write the application password or the Novamira setup prompt into any file.
