<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

/**
 * Diagnostics
 */
final class Diagnostics // phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
{
    private const ACCESS_NONE = 'none';
    private const ACCESS_READ = 'read';
    private const ACCESS_WRITE = 'write';
    private const ACCESS_READWRITE = 'readwrite';

    private array $log = [];

    private function __log(string $type, string $message, array $context = [], string $location = '', bool $datetime = true): void
    {
        $item = [
            'type' => $type,
            'message' => $message,
        ];
        if ($datetime) {
            $item['datetime'] = [date('d.m.Y H:i:s'), time()];
        }
        $locationTrim = trim($location);
        if (!empty($locationTrim)) {
            $item['location'] = $locationTrim;
        }
        unset($context['type']);
        unset($context['message']);
        unset($context['datetime']);
        unset($context['location']);
        $item = array_merge($item, $context);
        $this->log[] = $item;
    }

    private function checkProjectFileSystem(): bool
    {
        $this->__log('info', 'Checking Project File System', [], 'Diagnostics::checkProjectFileSystem#00');
        return true;
    }

    private function dirStatus(string $path): string
    {
        if (!file_exists($path) || !is_dir($path)) {
            return self::ACCESS_NONE;
        }
        if (!is_readable($path) && !is_writable($path)) {
            return self::ACCESS_NONE;
        }
        if (is_readable($path) && !is_writable($path)) {
            return self::ACCESS_READ;
        }
        if (!is_readable($path) && is_writable($path)) {
            return self::ACCESS_WRITE;
        }
        if (is_readable($path) && is_writable($path)) {
            return self::ACCESS_READWRITE;
        }
        return self::ACCESS_NONE;
    }

    private function checkDirectoryAccesses(): bool
    {
        $this->__log('info', 'Checking Directory Accesses', [], 'Diagnostics::checkDirectoryAccesses#00');
        $dirs = [
            Configuration::BLOG_ROOT => self::ACCESS_READ,
            Configuration::BLOG_DIR_SYSTEM => self::ACCESS_READ,
            Configuration::BLOG_DIR_PUBLIC => self::ACCESS_READ,
            Configuration::BLOG_DIR_CACHE => self::ACCESS_READWRITE,
            Configuration::BLOG_DIR_LOGS => self::ACCESS_READWRITE,
            Configuration::BLOG_DIR_PAGES => self::ACCESS_READ,
            Configuration::BLOG_DIR_POSTS => self::ACCESS_READ,
            Configuration::BLOG_DIR_ERRORS => self::ACCESS_READ,
        ];

        foreach ($dirs as $dirPath => $dirAccessExpected) {
            $status = $this->dirStatus($dirPath);
            if ($status !== $dirAccessExpected) {
                $this->__log('error', 'Incorrect Directory Access', ['path' => $dirPath, 'expected' => $dirAccessExpected, 'actual' => $status], 'Diagnostics::checkDirectoryAccesses#01');
                return false;
            }

            $this->__log('success', 'Correct Directory Access', [], 'Diagnostics::checkDirectoryAccesses#02');
        }
        $this->__log('success', 'All Directory Accesses Are Correct', [], 'Diagnostics::checkDirectoryAccesses#03');
        return true;
    }

    /**********
     * PUBLIC *
     **********/

    public function exportLog(): string
    {
        return (string) json_encode($this->log, JSON_PRETTY_PRINT);
    }

    public function run(): bool
    {
        if (!$this->checkProjectFileSystem()) {
            $this->__log('error', 'Step 1 Check Project file System failed', [], 'Diagnostics::run#00');
            return false;
        }
        $this->__log('success', 'Step 1 Check Project file System passed', [], 'Diagnostics::run#01');

        if (!$this->checkDirectoryAccesses()) {
            $this->__log('error', 'Step 2 Check Directory Accesses failed', [], 'Diagnostics::run#10');
            return false;
        }
        $this->__log('success', 'Step 2 Check Directory Accesses passed', [], 'Diagnostics::run#11');

        return true;
    }
}
