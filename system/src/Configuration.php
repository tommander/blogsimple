<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

final class Configuration
{
    public const SITE_URL = 'http://localhost:3737';
    public const BLOG_LOCALE = 'cs-CZ';
    public const BLOG_TITLE = 'Blog';
    public const BLOG_FOOTER = '&copy; 2026 Já.';
    public const BLOG_ROOT = __DIR__ . '/../../';

    public const BLOG_DIRNAME_SYSTEM = 'system';
    public const BLOG_DIRNAME_PUBLIC = 'public';

    public const BLOG_DIRNAME_CACHE = 'cache';
    public const BLOG_DIRNAME_LOGS = 'logs';
    public const BLOG_DIRNAME_PAGES = 'pages';
    public const BLOG_DIRNAME_POSTS = 'posts';
    public const BLOG_DIRNAME_ERRORS = 'errors';

    public const BLOG_DIR_SYSTEM = self::BLOG_ROOT . self::BLOG_DIRNAME_SYSTEM . '/';
    public const BLOG_DIR_PUBLIC = self::BLOG_ROOT . self::BLOG_DIRNAME_PUBLIC . '/';

    public const BLOG_DIR_CACHE = self::BLOG_DIR_SYSTEM . self::BLOG_DIRNAME_CACHE . '/';
    public const BLOG_DIR_LOGS = self::BLOG_DIR_SYSTEM . self::BLOG_DIRNAME_LOGS . '/';
    public const BLOG_DIR_PAGES = self::BLOG_DIR_PUBLIC . self::BLOG_DIRNAME_PAGES . '/';
    public const BLOG_DIR_POSTS = self::BLOG_DIR_PUBLIC . self::BLOG_DIRNAME_POSTS . '/';
    public const BLOG_DIR_ERRORS = self::BLOG_DIR_PUBLIC . self::BLOG_DIRNAME_ERRORS . '/';

    public const BLOG_ARCHIVE_TIME = (7 * 86400); // posts - auto archiving **7 days** after modification
    public const BLOG_CACHE_TTL = 60; // post/page/error rendered HTML refreshes **60 s** after previous modification
    public const BLOG_MAX_PAGES_IN_NAV = 6;
    public const BLOG_MD_CONVERTER_CONFIG = [
        'allow_unsafe_links' => false,
        'max_nesting_level' => 25,
        'max_delimiters_per_line' => 15,
        'disallowed_raw_html' => [
            'disallowed_tags' => [
                'title', 'textarea', 'style', 'xmp', 'iframe', 'noembed', 'noframes',
                'script', 'plaintext', 'embed', 'object', 'audio', 'video', 'body',
                'head', 'form', 'input', 'textarea', 'output', 'select', 'button',
            ],
        ],
    ];
    /** @var bool */
    public const BLOG_NO_CACHE = true;
    /** @var bool */
    public const BLOG_NO_LOG = false;
}
