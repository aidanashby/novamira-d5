# User Context (gate c)

Saved to the option `novamira_instructions_content` (and its legacy copy) on the Studio site. Novamira adds it to the start of every agent session. Visible in wp-admin under Novamira, Context, User context.

## Current text (revision approved by the user and live since 14 October 2026, 23:33)

Changes the publishing rule: agents may publish new or draft pages when asked, and must never delete, trash or unpublish a page. Everything else is unchanged.

```
This is a local Divi 5 development site. Divi 5 is the page builder for every page, template and layout, so do not ask which builder or approach to use. Use native Divi 5 modules and settings. Do not use a Code module or injected HTML, CSS or JavaScript for layout. Before reading or writing Divi content, load the divi5-core skill and follow it. Check the site itself (the block registry, Divi's module.json files and saved pages) before relying on remembered syntax. Build new pages as drafts. Publish a new or draft page only when asked. Never delete, trash or unpublish a page, and never overwrite an existing page unless asked. Do not create or activate a Novamira design, and do not write to Divi's global colours, variables, presets or components unless asked. After any write, read it back and report what changed. Never modify the Novamira plugin, or the application passwords or connections of the connected user.
```

## Previous text (approved at gate c, live 14 October 2026 until the revision)

```
This is a local Divi 5 development site. Divi 5 is the page builder for every page, template and layout, so do not ask which builder or approach to use. Use native Divi 5 modules and settings. Do not use a Code module or injected HTML, CSS or JavaScript for layout. Before reading or writing Divi content, load the divi5-core skill and follow it. Check the site itself (the block registry, Divi's module.json files and saved pages) before relying on remembered syntax. Build new pages as drafts, and never publish or overwrite an existing page unless asked. Do not create or activate a Novamira design, and do not write to Divi's global colours, variables, presets or components unless asked. After any write, read it back and report what changed. Never modify the Novamira plugin, or the application passwords or connections of the connected user.
```

## Why each line

Novamira's system context otherwise makes the agent ask which builder to use, the built-in design skill can trigger a DESIGN.md capture and save before any visual build, and the connection-safety line protects the link. The publishing rule follows the user's instruction of 14 October 2026.
