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
    public const BLOG_DIR_CACHE = self::BLOG_ROOT . 'cache/';
    public const BLOG_DIR_LOGS = self::BLOG_ROOT . 'logs/';
    public const BLOG_DIR_PAGES = self::BLOG_ROOT . 'pages/';
    public const BLOG_DIR_POSTS = self::BLOG_ROOT . 'posts/';
    public const BLOG_DIR_ERRORS = self::BLOG_ROOT . 'errors/';
    public const BLOG_DIR_TRASH = self::BLOG_ROOT . 'trash/';
    public const BLOG_ARCHIVE_TIME = (9*3600); // posts - auto archiving 9 hrs after modification

}
