<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class Cache
{
    use LoggerAwareTrait;

    public private(set) int $hits = 0;
    public private(set) int $cacheRead = 0;
    public private(set) int $cacheWrite = 0;
    public private(set) int $misses = 0;
    public private(set) int $ioRead = 0;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

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

    public function reset(string|null $dirname = null, string|null $filename = null): void
    {
        $list = scandir(Configuration::BLOG_DIR_CACHE, SCANDIR_SORT_NONE);
        foreach ($list as $file) {
            if (in_array($file, ['.', '..']) || !str_starts_with($file, 'cache_') || !str_ends_with($file, '.html')) {
                continue;
            }

            $oldPath = Configuration::BLOG_DIR_CACHE . $file;
            $newPath = Configuration::BLOG_DIR_TRASH . sprintf('litter-%.2d', mt_rand(0, 99));

            if (is_string($dirname) && is_string($filename)) {
                $this->logger && $this->logger->info('Resetting specific files "{dir}" and "{file}".', ['dir' => $dirname, 'file' => $filename]);
                $cacheFile = sprintf('cache_%1$s_%2$s.html', $dirname, $filename);
                
            }

            if (is_file($oldPath) && is_readable($oldPath) && is_writeable(dirname($newPath,))) {
                rename($oldPath, $newPath);
                $this->logger && $this->logger->info('Cache file "{old}" trashed as "{new}".', ['old' => $oldPath, 'new' => $newPath]);
                continue;
            }
            $this->logger && $this->logger->warning('Cache file "{old}" NOT trashed as "{new}".', ['old' => $oldPath, 'new' => $newPath]);
        }
    }

    public function get(string $dirName, string $fileName, callable $createValue): string
    {
        $this->logger && $this->logger->debug('Cache get "{dir}" and "{file}".', ['dir' => $dirName, 'file' => $fileName]);
        $safeName = sprintf(
            'cache_%1$s_%2$s.html',
            $dirName,
            $fileName,
        );
        $cacheFile = Configuration::BLOG_DIR_CACHE . $safeName;
        $sourceFile = Configuration::BLOG_DIR_PUBLIC . '/' . $dirName . '/' . $fileName . '.md';
        $this->logger && $this->logger->debug('Cache get "{dir}" and "{file}".', ['dir' => $cacheFile, 'file' => $sourceFile]);
        $this->logger && $this->logger->info('This is cache "{cache}" and source "{src}"', ['cache' => $cacheFile, 'src' => $sourceFile]);

        if (
            file_exists($cacheFile) &&
            is_file($cacheFile) &&
            is_readable($cacheFile) &&
            (time() - filemtime($cacheFile)) < Configuration::BLOG_CACHE_TTL
        ) {
            $this->hits++;
            $content = (string) file_get_contents($cacheFile);
            $this->cacheRead += strlen($content);
            return $content;
        }

//        $this->reset($dirName, $fileName);
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
