<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

final class Cache
{
    use LoggerAwareTrait;

    public int $hits = 0;
    public int $misses = 0;
    public int $cacheRead = 0;
    public int $cacheWrite = 0;

    public function __construct(LoggerInterface|null $logger)
    {
        if (Configuration::BLOG_NO_CACHE === true) {
            throw new \Error('Cannot create Cache instance when cache is disabled in config.');
        }
        $logger && $this->setLogger($logger);
    }

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

    public function reset(string|null $dirname = null, string|null $filename = null): void
    {
        if (is_string($dirname) && is_string($filename)) {
            $this->logger && $this->logger->info('Resetting specific files "{dir}" and "{file}".', ['dir' => $dirname, 'file' => $filename]);
            $cacheFile = sprintf('cache_%1$s_%2$s.html', $dirname, $filename);
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

    public function get(string $dirName, string $fileName): string|null
    {
        $cacheFile = Configuration::BLOG_DIR_CACHE . sprintf(
            'cache_%1$s_%2$s.html',
            $dirName,
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
