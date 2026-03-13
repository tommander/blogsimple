<?php

namespace Tommander\BlogSimple;

use Stringable;

final class Helper
{
    public static function esclang(string $lang): string
    {
        return preg_replace('/[^A-Za-z0-9-]/', '', $lang) ?? '';
    }

    public static function niceInterval(int $seconds): string
    {
        $prepend = (($seconds < 0) ? '-' : '');
        ($seconds < 0) && ($seconds *= -1);
            $text = match (true) {
                $seconds < 60 => sprintf('%d sec', $seconds),
                $seconds < 3600 => sprintf('%.0f min', intdiv($seconds, 60)),
                $seconds < 86400 => sprintf('%.0f hr', intdiv($seconds, 3600)),
                $seconds < (2 * 86400) => '1 day',
                $seconds >= (2 * 86400) => sprintf('%.0f days', intdiv($seconds, 86400)),
            };
        return $prepend . $text;
    }

    public static function niceIntervalNs(int $nanoseconds): string
    {
        $oneMicrosecond = 1000;
        $oneMillisecond = 1000000;
        $oneSecond = 1000000000;
        $prepend = (($nanoseconds < 0) ? '-' : '');
        ($nanoseconds < 0) && ($nanoseconds *= -1);
            $text = match (true) {
                $nanoseconds < $oneMicrosecond => sprintf('%d ns', $nanoseconds),
                $nanoseconds < $oneMillisecond => sprintf('%d μs', intdiv($nanoseconds, $oneMicrosecond)),
                $nanoseconds < $oneSecond => sprintf('%d ms', intdiv($nanoseconds, $oneMillisecond)),
                $nanoseconds < (60 * $oneSecond) => sprintf('%d s', intdiv($nanoseconds, $oneSecond)),
                $nanoseconds < (3600 * $oneSecond) => sprintf('%.0f min', intdiv($nanoseconds, 60 * $oneSecond)),
                $nanoseconds < (86400 * $oneSecond) => sprintf('%.0f hod', intdiv($nanoseconds, 3600 * $oneSecond)),
                $nanoseconds < (2 * 86400 * $oneSecond) => '1 den',
                $nanoseconds < (5 * 86400 * $oneSecond) => sprintf('%.0f dny', intdiv($nanoseconds, 86400 * $oneSecond)),
                $nanoseconds >= (5 * 86400 * $oneSecond) => sprintf('%.0f dni', intdiv($nanoseconds, 86400 * $oneSecond)),
            };
        return 'Render time (backend): ' . $prepend . $text;
    }

    // public static function niceNumber(float $num, string $baseUnit, int $precision = 1): string
    // {
    //     if (abs($num) < (10 ** -6)) {
    //         /** @psalm-suppress InvalidArgument */
    //         return sprintf('%.*f n%s', $precision, $num * (float)(10 ** 9), $baseUnit);
    //     }
    //     if (abs($num) < (10 ** -3)) {
    //         /** @psalm-suppress InvalidArgument */
    //         return sprintf('%.*f μ%s', $precision, $num * (float)(10 ** 6), $baseUnit);
    //     }
    //     if (abs($num) < 1) {
    //         /** @psalm-suppress InvalidArgument */
    //         return sprintf('%.*f m%s', $precision, $num * (float)(10 ** 3), $baseUnit);
    //     }

    //     if (abs($num) < (10 ** 3)) {
    //         /** @psalm-suppress InvalidArgument */
    //         return sprintf('%.*f %s', $precision, $num, $baseUnit);
    //     }

    //     if (abs($num) < (10 ** 6)) {
    //         /** @psalm-suppress InvalidArgument */
    //         return sprintf('%.*f k%s', $precision, $num / (float)(10 ** 3), $baseUnit);
    //     }

    //     if (abs($num) < (10 ** 9)) {
    //         /** @psalm-suppress InvalidArgument */
    //         return sprintf('%.*f M%s', $precision, $num / (float)(10 ** 6), $baseUnit);
    //     }

    //         /** @psalm-suppress InvalidArgument */
    //     return sprintf('%.*f G%s', $precision, $num / (float)(10 ** 9), $baseUnit);
    // }

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

    public static function anyToStr(mixed $something, string $default = '', bool $jsonBeforeDefault = true): string
    {
        if (is_string($something)) {
            return $something;
        }
        if (is_scalar($something) || is_null($something)) {
            return strval($something);
        }
        if (is_object($something) && (($something instanceof \Stringable) || method_exists($something, '__toString'))) {
            $result = '';
            try {
                /** @var mixed */
                $raw = $something->__toString();
                if (is_string($raw)) {
                    $result = $raw;
                }
            } catch (\Throwable $error) {
                $result = '';
            }
            return $result;
        }
        if ($jsonBeforeDefault) {
            $json = json_encode($something);
            if (json_last_error() === JSON_ERROR_NONE && is_string($json)) {
                return $json;
            }
        }
        return $default;
    }
}
