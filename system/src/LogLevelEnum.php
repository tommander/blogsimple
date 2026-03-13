<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LogLevel;

enum LogLevelEnum: string
{
    case EMERGENCY = LogLevel::EMERGENCY;
    case ALERT = LogLevel::ALERT;
    case CRITICAL = LogLevel::CRITICAL;
    case ERROR = LogLevel::ERROR;
    case WARNING = LogLevel::WARNING;
    case NOTICE = LogLevel::NOTICE;
    case INFO = LogLevel::INFO;
    case DEBUG = LogLevel::DEBUG;

    public function asString(): string
    {
        return match ($this) { // phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
            self::EMERGENCY => 'emergency',
            self::ALERT     => 'alert',
            self::CRITICAL  => 'critical',
            self::ERROR     => 'error',
            self::WARNING   => 'warning',
            self::NOTICE    => 'notice',
            self::INFO      => 'info',
            self::DEBUG     => 'debug',
        };
    }
}
