<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

enum FileTypeEnum: string
{
    case Pages = Configuration::BLOG_DIRNAME_PAGES;
    case Posts = Configuration::BLOG_DIRNAME_POSTS;
    case Errors = Configuration::BLOG_DIRNAME_ERRORS;

    public function path(): string
    {
        return match ($this) { // phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
            self::Pages => Configuration::BLOG_DIR_PAGES,
            self::Posts => Configuration::BLOG_DIR_POSTS,
            self::Errors => Configuration::BLOG_DIR_ERRORS,
        };
    }
}
