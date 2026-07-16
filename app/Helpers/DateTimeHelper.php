<?php

class DateTimeHelper
{
    public static function timezone(): DateTimeZone
    {
        return new DateTimeZone(
            defined('APP_TIMEZONE')
                ? APP_TIMEZONE
                : 'Asia/Dubai'
        );
    }

    public static function now(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            'now',
            self::timezone()
        );
    }

    public static function greeting(): string
    {
        $hour = (int)self::now()->format('G');

        if ($hour < 12) {
            return 'Good Morning';
        }

        if ($hour < 17) {
            return 'Good Afternoon';
        }

        return 'Good Evening';
    }

    public static function format(
        ?string $dateTime,
        string $format = 'd M Y, h:i A'
    ): string {
        if (empty($dateTime)) {
            return '-';
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | Database timestamps are currently treated as Dubai local time
            |--------------------------------------------------------------------------
            |
            | Use this while MySQL and PHP both store Dubai-local timestamps.
            |
            */

            $date = new DateTimeImmutable(
                $dateTime,
                self::timezone()
            );

            return $date->format($format);

        } catch (Throwable $e) {
            error_log(
                'DateTimeHelper formatting error: ' .
                $e->getMessage()
            );

            return $dateTime;
        }
    }

    public static function dateTime(): string
    {
        return self::now()->format(
            'l, d F Y · h:i A'
        );
    }

    public static function databaseNow(): string
    {
        return self::now()->format(
            'Y-m-d H:i:s'
        );
    }
}