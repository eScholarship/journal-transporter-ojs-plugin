<?php namespace JournalTransporterPlugin\Utility;

class Str {
    /**
     *
     */
    const CAMEL_TO_SNAKE_EXCEPTIONS = ['iPaddress' => 'ip_address'];

    /**
     * @param $str
     * @return mixed|string
     */
    public static function camelToSnake($str)
    {
        if(array_key_exists($str, self::CAMEL_TO_SNAKE_EXCEPTIONS)) return self::CAMEL_TO_SNAKE_EXCEPTIONS[$str];

        if (empty($str)) {
            return $str;
        }
        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", '_' . "$0", $str);
        return strtolower($str);
    }
}