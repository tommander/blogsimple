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
        if (file_exists($this->path) && is_file($this->path)) {
            $this->log = (string) file_get_contents($this->path);
        }
        $this->debug('Logger says hello');
    }

    public function __destruct()
    {
        $this->debug('Logger says bye' . PHP_EOL);
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
        $levelText = match ($level) {
            \Psr\Log\LogLevel::EMERGENCY => 'EMERGENCY',
            \Psr\Log\LogLevel::ALERT => 'ALERT',
            \Psr\Log\LogLevel::CRITICAL => 'CRITICAL',
            \Psr\Log\LogLevel::ERROR => 'ERROR',
            \Psr\Log\LogLevel::WARNING => 'warning',
            \Psr\Log\LogLevel::NOTICE => 'notice',
            \Psr\Log\LogLevel::INFO => 'info',
            default => '',
        };
        $dateText = date('c');
        $prefix = sprintf('{%1$s}[%2$s] ', $dateText, $levelText);
        $this->log .= $prefix . preg_replace_callback(
            '/\{(?<name>' . implode('|', array_keys($context)) . ')\}/',
            fn ($matches) => ($context[$matches['name'] ?? null] ?? ''),
            $message
        ) . PHP_EOL;
    }
}
