# Blog (simple)

[![PHP QA](https://github.com/tommander/blogsimple/actions/workflows/php-qa.yml/badge.svg)](https://github.com/tommander/blogsimple/actions/workflows/php-qa.yml)

Simple [PHP](https://php.net/) [blog](https://en.wikipedia.org/wiki/Blog) with [Markdown parser](https://github.com/league/commonmark). No [Javascript](https://developer.mozilla.org/en-US/docs/Glossary/JavaScript), only [HTML](https://developer.mozilla.org/en-US/docs/Web/HTML) and [CSS](https://developer.mozilla.org/en-US/docs/Web/CSS).

> [!IMPORTANT]
> Under construction (frequent breaking changes).

## PHP dependencies

**\*** = dev-only

- `php >= 8.2`
- [league/commonmark](https://github.com/league/commonmark)
- [vimeo/psalm](https://github.com/vimeo/psalm) **\***
- [squizlabs/php_codesniffer](https://github.com/squizlabs/php_codesniffer) **\***
- [phpcompatibility/php-compatibility](https://github.com/phpcompatibility/php-compatibility) **\***

## Installation

1. `git clone https://github.com/tommander/blogsimple.git`
2. `composer install`

## Folder structure

```
+ root
|
+---+ public/ # Web document root and place for all website assets
|   |
|   +---+ pages/ # Pages in Markdown format, e.g. "home.md"
|   |
|   +---+ posts/ # Posts in Markdown format, e.g. "bad-ass.md"
|   |
|   +---+ index.php # Web main index file
|   |
|   +---+ style.css # Main styles
|
+---+ system/ # System folder for backend code and data
|   |
|   +---+ cache/ # Cached HTML files (parsed Markdown files)
|   |
|   +---+ logs/ # Logs
|   |
|   +---+ partials/ # Partial HTML files in .php
|   |
|   +---+ src/ # BlogSimple PHP source code
|   |
|   +---+ tests/ # Unit tests, fixtures etc.
|
...project-related non-public files
```

## Configuration

Check `system/src/Configuration.php` for blog url, title etc. You probably don't need to change directory names / paths.

## Contents & Design

The HTML starts in `public/index.php`, which loads the partials (header, navigation, contents and footer) in `system/partials/`.

Class `Main` is the one that prepares page contents. It just tries to find the requested page/post (falling back to homepage),
do some replacements (e.g. for list of posts) and then it transforms MD to HTML.

There are simple styles in `public/style.css` that can be moved in its own folder and heavily improved. This is just a default.

## Pages and Posts

Create/edit/rename/move/copy/delete respective .md files in your favourite editor.

Pages and posts have just one difference - posts are showing last modification time (in list and detail).

Posts folder (`public/posts`) can be empty. As for pages, at least `public/pages/home.md` should exist, but there are already two pages forr posts - `public/pages/list.md` (non-archived posts) and `public/pages/archive.md` (archived posts).

## Run simple lighttpd local server

Prerequisite: [lighttpd](https://www.lighttpd.net/).

The document root is in the `public/` directory (where all website assets should be placed). The root folder, `vendor/`, `system/` etc. shouldn't be accessible to website visitor.

The default configuration is just some minimum decent server for development purposes.

1. `cp lighttpd.conf.default lighttpd.conf`
2. Edit lighttpd.conf
3. `lighttpd -tt -f lighttpd.conf`
4. `lighttpd -D -f lighttpd.conf`

## Documentation

*In progress*

## License

[CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/)

Photo [/public/images/lavender.webp](public/images/lavender.webp) by <a href="https://unsplash.com/@ettocl?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Léonard Cotte</a> on <a href="https://unsplash.com/photos/lavender-field-c1Jp-fo53U8?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>.
