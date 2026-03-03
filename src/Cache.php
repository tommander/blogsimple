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
    /** @var array<string, int> */
    public private(set) array $data = [];

    public function get(string $relFilePath, callable $createValue): string
    {
        $safeForFileName = fn (string $text) => preg_replace('/[^A-Za-z0-9_\.-]/', '', $text);
        $cacheFile = __DIR__ . '/../cache/' . $safeForFileName($relFilePath);
        $sourceFile = __DIR__ . "/../{$relFilePath}";

        if (
            isset($this->data[$relFilePath]) &&
            file_exists($cacheFile) &&
            is_file($cacheFile) &&
            is_readable($cacheFile) &&
            (time() - $this->data[$relFilePath]) < static::CACHE_TTL
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
        $this->data[$relFilePath] = time();
        return $md;
    }
}
