<?php namespace CdlExportPlugin\Utility;

class DataObjectUtility {
    /**
     * Given a resultSet returns an array of data objects cast as arrays
     * @param $resultSet
     * @return array|array[]|\stdClass[]
     */
    public static function resultSetToArray($resultSet, $exclude = ['allData']) {
        return array_map(
            function($dataObject) use($exclude) {
              return DataObjectUtility::dataObjectToArray($dataObject, $exclude);
            },
            $resultSet->toArray()
        );
    }

    /**
     * Given a DataObject, iterates the getters and builds an array of the results. If the getters return
     * a DataObject, we recurse. If it's not given a DO or an array, it returns the value.
     * @param $dataObject
     * @return mixed
     */
    public static function dataObjectToArray($dataObject, $exclude = ['allData']) {
        if(is_object($dataObject) && is_subclass_of($dataObject, 'DataObject')) {
            $out = new \stdClass;
            $out->__class = get_class($dataObject);
            foreach(self::getGetters($dataObject) as $method) {
                $key = self::stripGetFromGetter($method);
                $output = $dataObject->$method();
                if(!in_array($key, $exclude)) $out->$key = self::dataObjectToArray($output);
            }
            return $out;
        } elseif(is_array($dataObject)) {
            $out = [];
            foreach ($dataObject as $k => $v) {
                $out[$k] = self::dataObjectToArray($v);
            }
            return $out;
        } else {
            return $dataObject;
        }
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