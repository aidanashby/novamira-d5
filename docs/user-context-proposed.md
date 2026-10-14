# Proposed User Context (gate c)

Saved to the option `novamira_instructions_content` (and its legacy copy) on the Studio site. Novamira adds it to the start of every agent session. Visible afterwards in wp-admin under Novamira, Context, User context.

```
This is a local Divi 5 development site. Divi 5 is the page builder for every page, template and layout, so do not ask which builder or approach to use. Use native Divi 5 modules and settings. Do not use a Code module or injected HTML, CSS or JavaScript for layout. Before reading or writing Divi content, load the divi5-core skill and follow it. Check the site itself (the block registry, Divi's module.json files and saved pages) before relying on remembered syntax. Build new pages as drafts, and never publish or overwrite an existing page unless asked. Do not create or activate a Novamira design, and do not write to Divi's global colours, variables, presets or components unless asked. After any write, read it back and report what changed. Never modify the Novamira plugin, or the application passwords or connections of the connected user.
```

## Changes from the docs/06 draft

- The `builderVersion` line moves into the `divi5-core` skill, next to the tested method.
- "Publish" and "components" are added to the limits.
- "The local Divi 5 documentation" becomes Divi's `module.json` files, because the site agent cannot reach the Projects folder, and the docs were wrong twice in testing (breakpoint direction, Save to Library location).
