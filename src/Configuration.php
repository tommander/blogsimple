<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

class Configuration
{
    public const SITE_URL = 'http://localhost/blogsimple';
    public const BLOG_LOCALE = 'cs-CZ';
    public const BLOG_TITLE = 'Blog';
    public const BLOG_FOOTER = '&copy; 2026 Já.';
    public const BLOG_ROOT = __DIR__ . '/../';
    public const BLOG_DIRNAME_CACHE = 'cache';
    public const BLOG_DIRNAME_LOGS = 'logs';
    public const BLOG_DIRNAME_PAGES = 'pages';
    public const BLOG_DIRNAME_POSTS = 'posts';
    public const BLOG_DIRNAME_ERRORS = 'errors';
    public const BLOG_DIRNAME_TRASH = 'trash';
    public const BLOG_DIR_CACHE = self::BLOG_ROOT.self::BLOG_DIRNAME_CACHE.'/';
    public const BLOG_DIR_LOGS = self::BLOG_ROOT.self::BLOG_DIRNAME_LOGS.'/';
    public const BLOG_DIR_PAGES = self::BLOG_ROOT.self::BLOG_DIRNAME_PAGES.'/';
    public const BLOG_DIR_POSTS = self::BLOG_ROOT.self::BLOG_DIRNAME_POSTS.'/';
    public const BLOG_DIR_ERRORS = self::BLOG_ROOT.self::BLOG_DIRNAME_ERRORS.'/';
    public const BLOG_DIR_TRASH = self::BLOG_ROOT.self::BLOG_DIRNAME_TRASH.'/';
    public const BLOG_ARCHIVE_TIME = (7*86400); // posts - auto archiving **7 days** after modification
    public const BLOG_CACHE_TTL = 60; // post/page/error rendered HTML refreshes **60 s** after previous modification

}
