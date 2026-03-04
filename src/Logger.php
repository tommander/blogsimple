<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    protected string $path;
    protected string $log;

    public function __construct()
    {
        $this->path = Configuration::BLOG_DIR_LOGS . 'log.txt';
        $this->log = '';
        if (file_exists($this->path) && is_file($this->path)) {
            $this->log = (string) file_get_contents($this->path);
        }
    }

    public function __destruct()
    {
        file_put_contents($this->path, $this->log);
        $this->path = '';
        $this->log = '';
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->log .= preg_replace_callback(
            '/\{(?<name>' . implode('|', array_keys($context)) . ')\}/',
            fn ($matches) => ($context[$matches['name'] ?? null] ?? ''),
            $message
        );
    }
}
