<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

class Cache
{
    public const CACHE_TTL = 60; // 60 s
    public private(set) int $hits = 0;
    public private(set) int $cacheRead = 0;
    public private(set) int $cacheWrite = 0;
    public private(set) int $misses = 0;
    public private(set) int $ioRead = 0;

    public static function cacheSize(): int
    {
        $list = scandir(__DIR__ . '/../cache/', SCANDIR_SORT_NONE);
        $total = 0;
        foreach ($list as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $path = __DIR__ . '/../cache/' . $file;
            $size = filesize($path);
            $total += $size;
        }
        return $total;
    }

    public function get(string $relFilePath, callable $createValue): string
    {
        $safeName = sprintf(
            'cache_%1$s_%2$s.html',
            trim(preg_replace('/_{2,}/', '_', preg_replace('/[^A-Za-z0-9_\.-]/', '_', $relFilePath)), '_'),
            hash('md4', $relFilePath),
        );
        $cacheFile = __DIR__ . '/../cache/' . $safeName;
        $sourceFile = __DIR__ . "/../{$relFilePath}";

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
