Tested on Divi 5.15.1 (14 October 2026): presets made in the Visual Builder and through PHP, applied to modules written through PHP, and checked in a browser. Load `divi5-core` first.

## Rules

- Prefer existing presets. Before styling a module, read the presets and apply one that fits. Keep the preset list lean: no near-duplicates, no preset for a one-off style.
- Create or edit a preset when a style will repeat across modules or pages and no existing preset fits. Propose it to the user first, every time: name, module or option group, the settings, and which modules would use it (for an edit, every page using the preset changes). Write it only after the user agrees.
- Never delete a preset, and never change a module's default preset.

## Read presets (read only)

```php
$G = implode(chr(92), ['ET', 'Builder', 'Packages', 'GlobalData', 'GlobalPreset']);
$data = $G::get_data();   // ['module' => [...], 'group' => [...]]
```

- `$data['module'][<block name>]` is `['default' => <id>, 'items' => [<id> => item]]`. An item has `id`, `name`, `moduleName`, `version`, `type` `module`, `created`, `updated` (milliseconds), and its settings in `attrs` and `styleAttrs` (the same attribute shapes as a module).
- `$data['group'][<group name>]`, for example `divi/font`, holds option-group presets. An item adds `groupName`, `moduleName` and `groupId` (for example `designTitleText`, the heading's Title Text group).
- The builder adds an empty default preset ("Button Preset 1") for each module and group it has seen. An empty default changes nothing.

## Apply a preset to a module

- Module preset: `'modulePreset' => ['<preset id>']` at the top level of the block's attributes, a list, beside `builderVersion`.
- Option-group preset: `'groupPreset' => ['<groupId>' => ['presetId' => ['<preset id>'], 'groupName' => '<group name>']]`, for example `['designTitleText' => ['presetId' => ['3n9e2tidxl'], 'groupName' => 'divi/font']]`.
- Only use IDs that exist in `get_data()`. Divi adds classes such as `preset--module--divi-button--<id>` to the module.
- A module's own settings override its preset (verified: a local background colour and a local title colour both won over the preset's). Set only what should differ from the preset. Do not repeat preset values on the module.

## Create or edit a preset (after the user agrees to your proposal)

Copy the current data, add or change one item, and save it the way Divi's own save endpoint does:

```php
$G = implode(chr(92), ['ET', 'Builder', 'Packages', 'GlobalData', 'GlobalPreset']);
$data = $G::get_data();
$backup = wp_json_encode($data);   // return this, so the change can be undone
$id = substr(base_convert(bin2hex(random_bytes(8)), 16, 36), 0, 10);
$now = (int) round(microtime(true) * 1000);
$attrs = ['module' => ['decoration' => ['background' => ['desktop' => ['value' => ['color' => '#f1f5f9']]]]]];
$data['module']['divi/blurb']['default'] = $data['module']['divi/blurb']['default'] ?? '';
$data['module']['divi/blurb']['items'][$id] = ['id' => $id, 'name' => 'Blurb card', 'moduleName' => 'divi/blurb',
    'version' => ET_BUILDER_VERSION, 'type' => 'module', 'created' => $now, 'updated' => $now,
    'attrs' => $attrs, 'styleAttrs' => $attrs];
$schema = [];
foreach ($data as $type => $subs) {
    foreach ($subs as $rec) {
        $schema[$type][] = ['default' => $rec['default'] ?? '', 'items' => array_values($rec['items'] ?? [])];
    }
}
$saved = $G::save_data($G::prepare_data($schema));
```

- `prepare_data()` sanitises the attributes; `save_data()` stores them and clears Divi's static CSS for the whole site, which then regenerates.
- A group preset item is `['type' => 'group', 'id' => ..., 'name' => ..., 'version' => ..., 'created' => ..., 'updated' => ..., 'groupName' => 'divi/font', 'moduleName' => 'divi/heading', 'groupId' => 'designTitleText', 'attrs' => ..., 'styleAttrs' => ...]`, stored under `$data['group']['divi/font']['items']`.
- To edit, change the item's `attrs` and `styleAttrs` together and set `updated`. Keep `default` as it was.
- Undo: save the backup back with the same `prepare_data()` and `save_data()` steps.

## Check

- Read `get_data()` back and confirm the item and every module's `default` are as intended.
- `Style::render()` returns module CSS only; preset CSS is printed separately, so a PHP render check shows the preset classes but not the preset's styles. Check the result in a browser.
- In a browser, check the modules that use the preset. For buttons, look at a screenshot: Divi animates button backgrounds, so a script reading the computed colour can report transparent even when the colour shows.

Adapted in part from divilovewp/divi5-skill (MIT, copyright 2026 Shashank Gupta, divilove.com), verified and rewritten for Divi 5.15.1. The skill's advice not to include `groupPreset` does not hold on 5.15.1.
