<?php namespace JournalTransporterPlugin\Builder\Mapper;

/**
 * Class NestedMapper
 * @package JournalTransporterPlugin\Builder\Mapper
 */
class NestedMapper
{
    public static function map($mappable, $context = null, $placeholder = false) {
        if($placeholder) return "PLACEHOLDER";

        if(is_array($mappable)) {
            $out = [];
            foreach($mappable as $item) {
                $out[] = self::map($item);
            }
        } elseif(is_object($mappable)) {
            if(get_class($mappable) === 'stdClass') {
                if(isset($mappable->__mapperClass)) {
                    $mappableClass = $mappable->__mapperClass;
                } else {
                    return $mappable;
                }
            } else {
                $mappableClass = ucfirst(get_class($mappable));
            }

            $className = '\\JournalTransporterPlugin\\Builder\\Mapper\\DataObject\\'.$mappableClass;
            if(class_exists($className)) {
                $out = $className::map($mappable, $context);
            } else {
                $out = "Couldn't find mapper " . $className;
            }
        } else {
            $out = $mappable;
        }
        return $out;
    }
}