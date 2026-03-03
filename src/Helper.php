<?php

namespace Tommander\BlogSimple;

class Helper
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
                $seconds < 60 => sprintf('%d s', $seconds),
                $seconds < 3600 => sprintf('%.0f min', intdiv($seconds, 60)),
                $seconds < 86400 => sprintf('%.0f hod', intdiv($seconds, 3600)),
                $seconds < (2*86400) => '1 den',
                $seconds < (5*86400) => sprintf('%.0f dny', intdiv($seconds, 86400)),
                $seconds >= (5*86400) => sprintf('%.0f dni', intdiv($seconds, 86400)),
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
                $nanoseconds < (2*86400 * $oneSecond) => '1 den',
                $nanoseconds < (5*86400 * $oneSecond) => sprintf('%.0f dny', intdiv($nanoseconds, 86400 * $oneSecond)),
                $nanoseconds >= (5*86400 * $oneSecond) => sprintf('%.0f dni', intdiv($nanoseconds, 86400 * $oneSecond)),
            };
        return 'Render time (backend): ' . $prepend . $text;
    }

    public static function niceNumber(float $num, string $baseUnit, int $precision = 1): string
    {
        if (abs($num) < (10**-6)) {
            return sprintf('%.*f n%s', $precision, $num*(10**9), $baseUnit);
        }
        if (abs($num) < (10**-3)) {
            return sprintf('%.*f μ%s', $precision, $num*(10**6), $baseUnit);
        }
        if (abs($num) < 1) {
            return sprintf('%.*f m%s', $precision, $num*(10**3), $baseUnit);
        }

        if (abs($num) < (10**3)) {
            return sprintf('%.*f %s', $precision, $num, $baseUnit);
        }

        if (abs($num) < (10**6)) {
            return sprintf('%.*f k%s', $precision, $num/(10**3), $baseUnit);
        }

        if (abs($num) < (10**9)) {
            return sprintf('%.*f M%s', $precision, $num/(10**6), $baseUnit);
        }

        return sprintf('%.*f G%s', $num/(10**9), $baseUnit);
    }

    public static function niceBytes(int $bytes): string
    {
        if ($bytes < 0) {
            $bytes = -1 * $bytes;
        }

        if ($bytes < (2**10)) {
            return sprintf('%d B', $bytes);
        }

        if ($bytes < (2**20)) {
            return sprintf('%d KiB', intdiv($bytes, 2**10));
        }

        if ($bytes < (2**30)) {
            return sprintf('%d MiB', intdiv($bytes, 2**20));
        }

        if ($bytes < (2**40)) {
            return sprintf('%d GiB', intdiv($bytes, 2**30));
        }

        return sprintf('%d TiB', intdiv($bytes, 2**40));
    }
}
