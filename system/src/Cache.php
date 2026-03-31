<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

/**
 * Caching of HTML files parsed from Markdown.
 */
final class Cache
{
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

    public static function niceBytes(int $bytes): string
    {
        if ($bytes < 0) {
            $bytes = -1 * $bytes;
        }

        if ($bytes < (2 ** 10)) {
            return sprintf('%d B', $bytes);
        }

        if ($bytes < (2 ** 20)) {
            return sprintf('%d KiB', intdiv($bytes, 2 ** 10));
        }

        if ($bytes < (2 ** 30)) {
            return sprintf('%d MiB', intdiv($bytes, 2 ** 20));
        }

        if ($bytes < (2 ** 40)) {
            return sprintf('%d GiB', intdiv($bytes, 2 ** 30));
        }

        return sprintf('%d TiB', intdiv($bytes, 2 ** 40));
    }

    /**
     * Returns a simple HTML with cache statistics, if the given param is a valid Cache instance.
     * Otherwise returns "cache disabled".
     *
     * @param mixed $instance Cache instance
     */
    public static function htmlStatus(mixed $instance): string
    {
        if (!($instance instanceof self)) {
            return Configuration::TEXT_CACHE_DISABLED_HTML;
        }

        return sprintf(
            Configuration::TEXT_CACHE_STATS_HTML,
            $instance->getHits(),
            $instance->getMisses(),
            self::niceBytes($instance->getCacheRead()),
            self::niceBytes($instance->getCacheWrite()),
            self::niceBytes(Cache::cacheSize()),
        );
    }

    /**
     * Constructor of Cache. Cannot be instantiated when caching is disabled in Configuration class.
     */
    public function __construct()
    {
        if (Configuration::BLOG_NO_CACHE === true) {
            throw new \Error(Configuration::TEXT_CACHE_DISABLED_ERROR);
        }
    }

    public function processActions(): void
    {
        $cache = $_GET['cache'] ?? null;
        if ($cache === 'reset') {
            $this->reset();
        }
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

    public static function cacheFilename(string $filename): string
    {
        return sprintf('cache_%1$s.html', $filename);
    }

    /**
     * Deletes all cache files (.html)

     * @param non-empty-string|null $filename
     */
    public function reset(string|null $filename = null): void
    {
        if (is_string($filename)) {
            $list = [self::cacheFilename($filename)];
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
                continue;
            }
        }
    }

    /**
     * Retrieve a cache file
     *
     * @param non-empty-string $filename
     *
     * @return string|null Returns the content of the cache file if found and not expired, `null` otherwise.
     */
    public function get(string $filename): string|null
    {
        $cacheFile = Configuration::BLOG_DIR_CACHE . self::cacheFilename($filename);

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
