<?php namespace CdlExportPlugin\Utility;

class DateUtility {
    /**
     * @param $dateString
     * @return string
     * @throws \Exception
     */
    public static function formatDateString($dateString) {
        return (new \DateTime($dateString, new \DateTimeZone('America/Los_Angeles')))
            ->format('c');
    }
}