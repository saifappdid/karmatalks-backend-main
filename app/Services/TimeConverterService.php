<?php

namespace App\Services;

use Carbon\Carbon;

class TimeConverterService
{
    protected $timeZone;

    public function __construct(string $timeZone = 'UTC')
    {
        $this->timeZone = $timeZone;
    }

    public function toUtcTimestamp(?string $date, string $time): int
    {
        $date = $date ?? now($this->timeZone)->format('Y-m-d');

        return Carbon::createFromFormat(
            'Y-m-d H:i:s',
            "$date $time",
            $this->timeZone
        )
            ->utc()
            ->timestamp;
    }

    public function toLocalTime(int $timestamp, string $format = 'H:i:s'): string
    {
        return Carbon::createFromTimestamp($timestamp, 'UTC')
            ->setTimezone($this->timeZone)
            ->format($format);
    }

    /**
     * Convert a local time to UTC formatted datetime string
     */
    public function localToUtcString(
        string $localDatetime,
        string $format = 'Y-m-d H:i:s'
    ): string {
        return Carbon::createFromFormat(
            $format,
            $localDatetime,
            $this->timeZone
        )
            ->utc()
            ->format($format);
    }

    /**
     * Convert UTC datetime string to local formatted time
     */
    public function utcToLocalString(
        string $utcDatetime,
        string $format = 'H:i:s'
    ): string {
        return Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $utcDatetime,
            'UTC'
        )
            ->setTimezone($this->timeZone)
            ->format($format);
    }
}
