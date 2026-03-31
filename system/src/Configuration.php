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
    public const SITE_URL = 'http://localhost:2345';

    /**
     * Blog title (header + title)
     */
    public const BLOG_TITLE = 'Blog';
    /**
     * Blog author icon (footer)
     */
    public const BLOG_FOOTER_ICON = '🧙‍♂️';
    /**
     * Blog author (footer)
     */
    public const BLOG_FOOTER = 'John "Anonymous" Doe';
    /**
     * Blog author website (footer)
     */
    public const BLOG_FOOTER_URL = 'http://example.com';

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
     * Directory for partials = website content parts (php)
     */
    public const BLOG_DIRNAME_PARTIALS = 'partials';
    /**
     * Directory for public files
     */
    public const BLOG_DIRNAME_PUBLIC = 'public';
    /**
     * Directory for articles (md)
     */
    public const BLOG_DIRNAME_ARTICLES = 'articles';

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
     * Path to system screens directory
     */
    public const BLOG_DIR_PARTIALS = self::BLOG_DIR_SYSTEM . self::BLOG_DIRNAME_PARTIALS . '/';
    /**
     * Path to public pages directory
     */
    public const BLOG_DIR_ARTICLES = self::BLOG_DIR_PUBLIC . self::BLOG_DIRNAME_ARTICLES . '/';

    /**
     * Number of seconds, after which a post is shown in Archive rather that Posts.
     * Default: 7 * 86400 sec = 7 days
     */
    public const BLOG_ARCHIVE_TIME = (7 * 86400);
    /**
     * Number of seconds after which a cache file expires
     */
    public const BLOG_CACHE_TTL = 60; // post/page rendered HTML refreshes **60 s** after previous modification
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

    /* * * * * *  * * * * *
     * TRANSLATABLE TEXTS *
     * * * * * *  * * * * */

    public const TEXT_CACHE_DISABLED_HTML = '';
    public const TEXT_CACHE_STATS_HTML = '<samp>Hits: %1$d</samp> <samp>Misses: %2$d</samp> <samp>CS: %5$s</samp> <samp>CR: %3$s</samp> <samp>CW: %4$s</samp>';
    public const TEXT_CACHE_DISABLED_ERROR = 'Cannot create Cache instance when cache is disabled in config.';
    public const TEXT_MAIN_SOURCE_FILE_ERROR_HTML  = '<h1>Error</h1><p>Source file <code>%1$s</code> does not exist or is not readable.</p>';
    public const TEXT_MAIN_DEBUG_PAGE_HTML = '<h1>Debug</h1><p>This page can be used by developers/testers - you can find it in <code>/system/src/Main.php</code> method <code>Main::debugPage()</code>. Add, edit and delete whatever you want below.</p>%1$s';
}
