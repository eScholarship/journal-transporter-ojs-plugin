<?php namespace CdlExportPlugin\Builder\Mapper;

/**
 * Class NestedMapper
 * @package CdlExportPlugin\Builder\Mapper
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
            $className = '\\CdlExportPlugin\\Builder\\Mapper\\DataObject\\'.ucfirst(get_class($mappable));
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