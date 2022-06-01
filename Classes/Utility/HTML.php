<?php namespace JournalTransporterPlugin\Utility;

class HTML {
    public static function cleanHtml($string) {
        $steps = [$string];
        $steps[] = strip_tags(end($steps), '<p><ul><li><ol><em><i><strong>');
        // A bullet-vulnerable way to strip attributes off of HTML tags
        $steps[] = preg_replace('/(<[a-zA-Z]+)[^>]*>/','\1>', end($steps));
        return end($steps);
    }
}