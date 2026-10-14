Tested on Divi 5.15.1 (14 October 2026): each shape below was written with `serialize_blocks()` and rendered, and those marked "builder" were also opened in the Visual Builder. Load `divi5-core` first. All values sit under `<bp>.value` as `{"desktop":{"value":...}}`; shown here as the desktop value only.

## Parent and child modules

A parent holds only its children; give the parent just `builderVersion` unless styling it.

| Parent | Child | Child attributes |
|---|---|---|
| `divi/accordion` | `divi/accordion-item` | `title.innerContent` text, `content.innerContent` HTML, `module.advanced.open` `"on"` for the open item (builder) |
| `divi/tabs` | `divi/tab` | `title.innerContent` text, `content.innerContent` HTML. The first tab is active. |
| `divi/slider` | `divi/slide` | `title.innerContent` text, `content.innerContent` HTML, `button.innerContent` `{text, linkUrl}` |
| `divi/icon-list` | `divi/icon-list-item` | `content.innerContent` text |
| `divi/contact-form` | `divi/contact-field` | see Contact form |

## Single modules

- Blurb (builder): `title.innerContent` `{text}`, `content.innerContent` HTML, `imageIcon.innerContent` `{src}`.
- Call to action (builder): `title.innerContent` text, `content.innerContent` HTML, `button.innerContent` `{text, linkUrl}`. Default background is the theme primary colour.
- Image (builder): `image.innerContent` `{src, id, alt, titleText, width, height}` (strings), from `wp_get_attachment_image_src($id, 'full')`.
- Toggle: `title.innerContent` text, `content.innerContent` HTML. Renders closed.
- Testimonial: `content.innerContent` HTML, `author.innerContent` text, `jobTitle.innerContent` text, `company.innerContent` `{text, linkUrl}`, `portrait.innerContent` `{src}`.
- Team member: `name.innerContent`, `position.innerContent` text, `content.innerContent` HTML, `image.innerContent` `{url}`. `src` does not render here; use `url`.

## Contact form

```php
$form = $b('divi/contact-form', [
    'module' => ['advanced' => ['uniqueId' => $d(wp_generate_uuid4())]],
    'title'  => ['innerContent' => $d('Get in touch')],
    'button' => ['innerContent' => $d(['text' => 'Send'])],
], [
    $b('divi/contact-field', ['fieldItem' => ['innerContent' => $d('Name'),
        'advanced' => ['id' => $d('name'), 'type' => $d('input'), 'required' => $d('on')]]]),
    $b('divi/contact-field', ['fieldItem' => ['innerContent' => $d('Email'),
        'advanced' => ['id' => $d('email'), 'type' => $d('email')]]]),
    $b('divi/contact-field', ['fieldItem' => ['innerContent' => $d('Message'),
        'advanced' => ['id' => $d('message'), 'type' => $d('text')]]]),
]);
```

- Verified field types: `input`, `email`, `text` (message box). `module.json` also lists `select`, `checkbox` and `radio` with options under `fieldItem.advanced.selectOptions`, `checkboxOptions` and `radioOptions`; verify those on this site. Give every field a unique `id`.
- Divi adds a maths CAPTCHA and the submit button itself.
- The recipient address is `email.innerContent`. Ask the user before setting it.

## Not verified here

Gallery (needs media IDs set in the builder), video, audio, maps, counters, pricing tables, menus, Loop Builder and dynamic content, and modules from plugins not installed (WooCommerce, Gravity Forms). For these, read the module's `module.json`, or ask the user to add one in the builder and copy its saved shape.

Adapted in part from divilovewp/divi5-skill (MIT, copyright 2026 Shashank Gupta, divilove.com), verified and rewritten for Divi 5.15.1.
