Tested on Divi 5.15.1 (14 October 2026): a template built in the Theme Builder, then its header and footer layouts edited through PHP and checked in a browser. Load `divi5-core` first; layouts use the same block format and recipes.

## How Divi stores Theme Builder data

- One `et_theme_builder` post (status publish) lists its templates in repeated meta `_et_template` (template post IDs).
- Each template is an `et_template` post. Meta: `_et_default` (`1` for the Default Website Template), `_et_enabled`, `_et_use_on` and `_et_exclude_from` (assignment rules, for example `singular:post_type:page:id:55`), and `_et_header_layout_id`, `_et_body_layout_id`, `_et_footer_layout_id` with matching `_enabled` flags. A layout ID of `0` means the theme default.
- Each layout is an `et_header_layout`, `et_body_layout` or `et_footer_layout` post whose `post_content` is normal Divi 5 block markup (placeholder, section, row, column, modules) with the usual builder meta.

Read the whole picture first:

```php
$tb = get_posts(['post_type' => 'et_theme_builder', 'post_status' => 'any', 'numberposts' => 1]);
$out = [];
foreach (get_post_meta($tb[0]->ID, '_et_template') as $tid) {
    $m = get_post_meta($tid);
    $out[] = ['id' => $tid, 'title' => get_the_title($tid), 'use_on' => $m['_et_use_on'] ?? [],
              'header' => $m['_et_header_layout_id'][0] ?? '0', 'body' => $m['_et_body_layout_id'][0] ?? '0',
              'footer' => $m['_et_footer_layout_id'][0] ?? '0'];
}
return $out;
```

## Edit a layout

1. Find the layout ID from its template. Say which pages the template applies to (`_et_use_on`) before editing: a layout edit goes live at once on every page it is assigned to, because layouts are published posts (verified: the edit showed on the live page straight away).
2. Ask the user before editing a layout used site-wide (the Default Website Template, or broad rules such as all pages), unless they already asked for that change.
3. Save the current `post_content` (return it, so it can be restored), then parse, modify and serialise exactly as for a page, and write it with `wp_update_post` and `wp_slash()`.
4. Call `ET_Core_PageResource::remove_static_resources($layout_id, 'all')`, and the same for a page that uses the template.
5. Check in a browser: the header is `.et-l--header`, the footer `.et-l--footer`, a body layout probably `.et-l--body` (not tested). Check one page the template should affect and one it should not.

## Do not, unless the user asks

Create or delete templates, change `_et_use_on` or `_et_exclude_from`, change which layouts a template points to, or edit the `et_theme_builder` post. These change which pages get which header and footer across the site. Divi's Theme Builder screen is the safer place for them.

Adapted in part from divilovewp/divi5-skill (MIT, copyright 2026 Shashank Gupta, divilove.com), verified and rewritten for Divi 5.15.1.
