<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

/**
 * Static common configuration for the blog
 */
final class Configuration
{
    /**
     * Site URL, i.e. this URL will load `/public/index.php` for a visitor and every link
     * on the site uses this as its base.
     *
     * Without trailing slash.
     */
    public const SITE_URL = 'http://localhost:3737';

    /**
     * BCP 47 language tag
     */
    public const BLOG_LOCALE = 'en-GB';
    /**
     * Blog title (header + title)
     */
    public const BLOG_TITLE = 'Blog';
    /**
     * Blog copyright (footer)
     */
    public const BLOG_FOOTER = '&copy; 2026 Já.';

    /**
     * Root folder of the blog ("where root composer.json is")
     */
    public const BLOG_ROOT = __DIR__ . '/../../';

    /**
     * Directory for system files
     */
    public const BLOG_DIRNAME_SYSTEM = 'system';
    /**
     * Directory for cache files (html)
     */
    public const BLOG_DIRNAME_CACHE = 'cache';
    /**
     * Directory for log files (txt)
     */
    public const BLOG_DIRNAME_LOGS = 'logs';

    /**
     * Directory for public files
     */
    public const BLOG_DIRNAME_PUBLIC = 'public';
    /**
     * Directory for public pages (md)
     */
    public const BLOG_DIRNAME_PAGES = 'pages';
    /**
     * Directory for public posts (md)
     */
    public const BLOG_DIRNAME_POSTS = 'posts';
    /**
     * Directory for public errors (md)
     */
    public const BLOG_DIRNAME_ERRORS = 'errors';

    /**
     * Path to system directory
     */
    public const BLOG_DIR_SYSTEM = self::BLOG_ROOT . self::BLOG_DIRNAME_SYSTEM . '/';
    /**
     * Path to public directory
     */
    public const BLOG_DIR_PUBLIC = self::BLOG_ROOT . self::BLOG_DIRNAME_PUBLIC . '/';

    /**
     * Path to system cache directory
     */
    public const BLOG_DIR_CACHE = self::BLOG_DIR_SYSTEM . self::BLOG_DIRNAME_CACHE . '/';
    /**
     * Path to system logs directory
     */
    public const BLOG_DIR_LOGS = self::BLOG_DIR_SYSTEM . self::BLOG_DIRNAME_LOGS . '/';
    /**
     * Path to public pages directory
     */
    public const BLOG_DIR_PAGES = self::BLOG_DIR_PUBLIC . self::BLOG_DIRNAME_PAGES . '/';
    /**
     * Path to public posts directory
     */
    public const BLOG_DIR_POSTS = self::BLOG_DIR_PUBLIC . self::BLOG_DIRNAME_POSTS . '/';
    /**
     * Path to public errors directory
     */
    public const BLOG_DIR_ERRORS = self::BLOG_DIR_PUBLIC . self::BLOG_DIRNAME_ERRORS . '/';

    /**
     * Number of seconds, after which a post is shown in Archive rather that Posts.
     * Default: 7 * 86400 sec = 7 days
     */
    public const BLOG_ARCHIVE_TIME = (7 * 86400);
    /**
     * Number of seconds after which a cache file expires
     */
    public const BLOG_CACHE_TTL = 60; // post/page/error rendered HTML refreshes **60 s** after previous modification
    /**
     * Up to how many pages to show in navigation under header.
     * Minimum is 3 due to pre-inserted pages Home, Posts, Archive.
     */
    public const BLOG_MAX_PAGES_IN_NAV = 6;
    /**
     * Settings for the MD -> HTML converter.
     */
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
    /**
     * Disables cache.
     *
     * @var bool
     */
    public const BLOG_NO_CACHE = true;
    /**
     * Disables logging.
     *
     * @var bool
     */
    public const BLOG_NO_LOG = false;
}
