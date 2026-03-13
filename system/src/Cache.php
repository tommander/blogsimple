<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Caching of HTML files parsed from Markdown.
 */
final class Cache
{
    use LoggerAwareTrait;

    /**
     * Number of hits = cached item served i/o needing to parse MD
     */
    private int $hits = 0;
    /**
     * Number of misses = freshly parsed MD served i/o cached item
     */
    private int $misses = 0;
    /**
     * Total bytes read from cache
     */
    private int $cacheRead = 0;
    /**
     * Total bytes written to cache
     */
    private int $cacheWrite = 0;

    /**
     * Returns a simple HTML with cache statistics, if the given param is a valid Cache instance.
     * Otherwise returns "cache disabled".
     *
     * @param mixed $instance Cache instance
     */
    public static function htmlStatus(mixed $instance): string
    {
        if (!($instance instanceof self)) {
            return '<samp>Cache Disabled</samp>';
        }

        return sprintf(
            '<samp>Hits: %1$d</samp> <samp>Misses: %2$d</samp> <samp>CS: %5$s</samp> <samp>CR: %3$s</samp> <samp>CW: %4$s</samp>',
            $instance->getHits(),
            $instance->getMisses(),
            Helper::niceBytes($instance->getCacheRead()),
            Helper::niceBytes($instance->getCacheWrite()),
            Helper::niceBytes(Cache::cacheSize()),
        );
    }

    /**
     * Constructor of Cache. Cannot be instantiated when caching is disabled in Configuration class.
     */
    public function __construct(LoggerInterface|null $logger)
    {
        if (Configuration::BLOG_NO_CACHE === true) {
            throw new \Error('Cannot create Cache instance when cache is disabled in config.');
        }
        $logger && $this->setLogger($logger);
    }

    public function getHits(): int
    {
        return $this->hits;
    }

    public function getMisses(): int
    {
        return $this->misses;
    }

    public function getCacheRead(): int
    {
        return $this->cacheRead;
    }

    public function getCacheWrite(): int
    {
        return $this->cacheWrite;
    }

    /**
     * Returns total size of all cache files (.html; in bytes)
     */
    public static function cacheSize(): int
    {
        $list = scandir(Configuration::BLOG_DIR_CACHE, SCANDIR_SORT_NONE);
        if (!is_array($list)) {
            $list = [];
        }
        $total = 0;
        foreach ($list as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $path = Configuration::BLOG_DIR_CACHE . $file;
            $size = (int)filesize($path);
            $total += $size;
        }
        return $total;
    }

    /**
     * Deletes all cache files (.html)

     * @param FileTypeEnum|null $dirname If `null`, delete cache files in all folders (posts, pages, errors)
     * @param non-empty-string|null $filename
     */
    public function reset(FileTypeEnum|null $dirname = null, string|null $filename = null): void
    {
        if (($dirname instanceof FileTypeEnum) && is_string($filename)) {
            $this->logger && $this->logger->info('Resetting specific files "{dir}" and "{file}".', ['dir' => $dirname->value, 'file' => $filename]);
            $cacheFile = sprintf('cache_%1$s_%2$s.html', $dirname->value, $filename);
            $list = [$cacheFile];
        } else {
            $list = scandir(Configuration::BLOG_DIR_CACHE, SCANDIR_SORT_NONE);
        }
        if (!is_array($list)) {
            $list = [];
        }
        foreach ($list as $file) {
            if (in_array($file, ['.', '..']) || !str_starts_with($file, 'cache_') || !str_ends_with($file, '.html')) {
                continue;
            }

            $oldPath = Configuration::BLOG_DIR_CACHE . $file;

            if (is_file($oldPath) && is_readable($oldPath) && is_writeable($oldPath)) {
                unlink($oldPath);
                $this->logger && $this->logger->info('Cache file "{old}" deleted.', ['old' => $oldPath]);
                continue;
            }
            $this->logger && $this->logger->warning('Cache file "{old}" NOT deleted.', ['old' => $oldPath]);
        }
    }

    /**
     * Retrieve a cache file
     *
     * @param FileTypeEnum $dirName
     * @param non-empty-string $fileName
     *
     * @return string|null Returns the content of the cache file if found and not expired, `null` otherwise.
     */
    public function get(FileTypeEnum $dirName, string $fileName): string|null
    {
        $cacheFile = Configuration::BLOG_DIR_CACHE . sprintf(
            'cache_%1$s_%2$s.html',
            $dirName->value,
            $fileName,
        );

        if (
            file_exists($cacheFile) &&
            is_file($cacheFile) &&
            is_readable($cacheFile) &&
            (time() - (int)filemtime($cacheFile)) < Configuration::BLOG_CACHE_TTL
        ) {
            $this->hits++;
            $content = (string) file_get_contents($cacheFile);
            $this->cacheRead += strlen($content);
            return $content;
        }

        $this->misses++;
        return null;
    }
}
