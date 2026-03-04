<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LoggerAwareTrait;

class Cache
{
    use LoggerAwareTrait;

    public const CACHE_TTL = 60; // 60 s
    public private(set) int $hits = 0;
    public private(set) int $cacheRead = 0;
    public private(set) int $cacheWrite = 0;
    public private(set) int $misses = 0;
    public private(set) int $ioRead = 0;

    public static function cacheSize(): int
    {
        $list = scandir(Configuration::BLOG_DIR_CACHE, SCANDIR_SORT_NONE);
        $total = 0;
        foreach ($list as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $path = Configuration::BLOG_DIR_CACHE . $file;
            $size = filesize($path);
            $total += $size;
        }
        return $total;
    }

    public function reset(): void
    {
        $list = scandir(Configuration::BLOG_DIR_CACHE, SCANDIR_SORT_NONE);
        foreach ($list as $file) {
            if (in_array($file, ['.', '..']) || !str_starts_with($file, 'cache_') || !str_ends_with($file, '.html')) {
                continue;
            }
            $oldPath = Configuration::BLOG_DIR_CACHE . $file;
            $newPath = Configuration::BLOG_DIR_TRASH . sprintf('%.3d', mt_rand(0, 999));
            if (is_file($oldPath) && is_readable($oldPath) && is_writeable(dirname($newPath,))) {
                rename($oldPath, $newPath);
                $this->logger && $this->logger->info('Cache file "{old}" trashed as "{new}".', ['old' => $oldPath, 'new' => $newPath]);
                continue;
            }
            $this->logger && $this->logger->warning('Cache file "{old}" NOT trashed as "{new}".', ['old' => $oldPath, 'new' => $newPath]);
        }
    }

    public function get(string $relFilePath, callable $createValue): string
    {
        $safeName = sprintf(
            'cache_%1$s_%2$s.html',
            trim(preg_replace('/_{2,}/', '_', preg_replace('/[^A-Za-z0-9_\.-]/', '_', $relFilePath)), '_'),
            hash('md4', $relFilePath),
        );
        $cacheFile = Configuration::BLOG_DIR_CACHE . $safeName;
        $sourceFile = Configuration::BLOG_ROOT . $relFilePath;

        if (
            file_exists($cacheFile) &&
            is_file($cacheFile) &&
            is_readable($cacheFile) &&
            (time() - filemtime($cacheFile)) < static::CACHE_TTL
        ) {
            $this->hits++;
            $content = (string) file_get_contents($cacheFile);
            $this->cacheRead += strlen($content);
            return $content;
        }

        $this->misses++;
        if (
            !file_exists($sourceFile) ||
            !is_file($sourceFile) ||
            !is_readable($sourceFile)
        ) {
            return '';
        }

        $raw = (string) file_get_contents($sourceFile);
        $this->ioRead += strlen($raw);
        $md = $createValue($raw);
        $this->cacheWrite += (int) file_put_contents($cacheFile, $md);
        return $md;
    }
}
