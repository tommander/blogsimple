# Blog (simple)

Simple [PHP](https://php.net/) [blog](https://en.wikipedia.org/wiki/Blog) with [Markdown parser](https://github.com/league/commonmark). No [Javascript](https://developer.mozilla.org/en-US/docs/Glossary/JavaScript), only [HTML](https://developer.mozilla.org/en-US/docs/Web/HTML) and [CSS](https://developer.mozilla.org/en-US/docs/Web/CSS).

> [!IMPORTANT]
> Under construction (frequent breaking changes).

## PHP dependencies 

**\*** = dev-only

- [league/commonmark](https://github.com/league/commonmark)
- [psr/log](https://github.com/psr/log) **\***
- [vimeo/psalm](https://github.com/vimeo/psalm) **\***
- [squizlabs/php_codesniffer](https://github.com/squizlabs/php_codesniffer) **\***
- [phpcompatibility/php-compatibility](https://github.com/phpcompatibility/php-compatibility) **\***

## Installation

Prerequisite: [lighttpd](https://www.lighttpd.net/).

1. `git clone https://github.com/tommander/blogsimple.git`

## Run lighttpd local server

3. `cp lighttpd.conf.default lighttpd.conf`
4. Edit lighttpd.conf
5. `lighttpd -tt -f lighttpd.conf`
6. `lighttpd -D -f lighttpd.conf`

## License

[CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/)

Photo [/public/images/lavender.webp](public/images/lavender.webp) by <a href="https://unsplash.com/@ettocl?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Léonard Cotte</a> on <a href="https://unsplash.com/photos/lavender-field-c1Jp-fo53U8?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>.
