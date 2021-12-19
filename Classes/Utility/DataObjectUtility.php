<?php namespace CdlExportPlugin\Utility;

class DataObjectUtility {
    /**
     * Given a data object, iterates thee getters and builds an array of the results
     * @param $dataObject
     * @return array
     */
    public static function dataObjectToArray($dataObject, $exclude = ['allData']) {
        $out = new \stdClass;
        foreach(self::getGetters($dataObject) as $method) {
            $key = self::stripGetFromGetter($method);
            if(!in_array($key, $exclude)) $out->$key = $dataObject->$method();
        }
        return $out;
    }

    /**
     *
     * @param string $methodName
     * @return string
     */
    public static function stripGetFromGetter(string $methodName) {
        if(substr($methodName, 0, 3) === 'get') {
            return strtolower($methodName[3]) . substr($methodName, 4);
        }
        return $methodName;
    }

    /**
     * Given an instance of a DataObject class, returns an array of all method names that begin with `get` and
     * take 0 arguments.
     * @param object $instance
     * @return mixed
     */
    public static function getGetters(object $instance) {
        $class = get_class($instance);
        return array_filter(
            get_class_methods($instance),
            function($methodName) use($class) {
                if(substr($methodName, 0, 3) !== 'get') return false;
                $methodReflection = new \ReflectionMethod($class, $methodName);
                return count($methodReflection->getParameters()) === 0;
            }
        );
    }
}