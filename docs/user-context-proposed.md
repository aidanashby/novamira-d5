# User Context (gate c)

Saved to the option `novamira_instructions_content` (and its legacy copy) on the Studio site. Novamira adds it to the start of every agent session. Visible in wp-admin under Novamira, Context, User context.

## Current text (approved at gate c, live since 15 October 2026, last changed 21 October 2026)

Adds one sentence routing new sites and redesigns to `divi5-design-system`, and lets agents propose presets, variables and global colours themselves and write them once the user agrees (approved 15 October 2026). On 21 October 2026 it gained the sentence excluding Novamira's built-in `novamira-design` skill (T24). The previous text below differs only in those places.

```
This is a Divi 5 development or staging copy of the site. Divi 5 is the page builder for every page, template and layout, so do not ask which builder or approach to use. Use native Divi 5 modules and settings. Do not use a Code module or injected HTML, CSS or JavaScript for layout. Before reading or writing Divi content, load the divi5-core skill and follow it. For a new website, a new set of pages or a redesign, load divi5-design-system first; if it calls for a design showcase, build no other content page until the user approves it. Do not load the built-in novamira-design skill; divi5-design-system does that job on this site. Check the site itself (the block registry, Divi's module.json files and saved pages) before relying on remembered syntax. Build new pages as drafts. Publish a new or draft page only when asked. Never delete, trash or unpublish a page, and never overwrite an existing page unless asked. Do not create or activate a Novamira design, and do not write to Divi's components unless asked. You may propose new or changed presets, variables and global colours yourself, and write them once the user agrees. Delete one only with the user's explicit approval, after explaining what it affects. After any write, read it back and report what changed. Never modify the Novamira plugin, or the application passwords or connections of the connected user.
```

## Previous text (live 14 October 2026, 23:33, until 15 October 2026)

Changed the publishing rule: agents may publish new or draft pages when asked, and must never delete, trash or unpublish a page.

```
This is a local Divi 5 development site. Divi 5 is the page builder for every page, template and layout, so do not ask which builder or approach to use. Use native Divi 5 modules and settings. Do not use a Code module or injected HTML, CSS or JavaScript for layout. Before reading or writing Divi content, load the divi5-core skill and follow it. Check the site itself (the block registry, Divi's module.json files and saved pages) before relying on remembered syntax. Build new pages as drafts. Publish a new or draft page only when asked. Never delete, trash or unpublish a page, and never overwrite an existing page unless asked. Do not create or activate a Novamira design, and do not write to Divi's global colours, variables, presets or components unless asked. After any write, read it back and report what changed. Never modify the Novamira plugin, or the application passwords or connections of the connected user.
```

## Original text (approved at gate c, live 14 October 2026 until the publishing revision)

```
This is a local Divi 5 development site. Divi 5 is the page builder for every page, template and layout, so do not ask which builder or approach to use. Use native Divi 5 modules and settings. Do not use a Code module or injected HTML, CSS or JavaScript for layout. Before reading or writing Divi content, load the divi5-core skill and follow it. Check the site itself (the block registry, Divi's module.json files and saved pages) before relying on remembered syntax. Build new pages as drafts, and never publish or overwrite an existing page unless asked. Do not create or activate a Novamira design, and do not write to Divi's global colours, variables, presets or components unless asked. After any write, read it back and report what changed. Never modify the Novamira plugin, or the application passwords or connections of the connected user.
```

## Why each line

Novamira's system context otherwise makes the agent ask which builder to use, the built-in design skill can trigger a DESIGN.md capture and save before any visual build, and the connection-safety line protects the link. The publishing rule follows the user's instruction of 14 October 2026.
