<?php namespace JournalTransporterPlugin\Utility;

class Date {
    /**
     * These might be configurable one day, but for now it's not
     */
    const READ_TIMEZONE = 'America/Los_Angeles';
    const WRITE_TIMEZONE = 'UTC';
    const WRITE_FORMAT = 'c';

    /**
     * @param $dateString
     * @return string
     * @throws \Exception
     */
    public static function formatDateString($dateString)
    {
        return self::strToDatetime($dateString)
            ->setTimezone(new \DateTimeZone(self::WRITE_TIMEZONE))
            ->format(self::WRITE_FORMAT);
    }

    /**
     * @param $dateString
     * @return \DateTime
     */
    public static function strToDatetime($dateString)
    {
        return new \DateTime($dateString, new \DateTimeZone(self::READ_TIMEZONE));
    }
}