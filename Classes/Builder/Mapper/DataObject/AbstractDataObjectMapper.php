<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use CdlExportPlugin\Builder\Mapper\NestedMapper;
use CdlExportPlugin\Utility\DateUtility;

class AbstractDataObjectMapper {

    /**
     * Where we store record identifiers in the JSON we output
     */
    const SOURCE_RECORD_KEY_PROPERTY = 'sourceRecordKey';

    /**
     * Extend this class, and name the child class after a OJS class. Add a static parameter called $mapping, which
     * is a string. Each line of the string contains a field name. If the field name is being mapped to a different
     * field that the source field name, use this syntax `sourceField -> targetField`.
     *
     * Don't be frightened by the oddly formatted mapping in those subclasses. It's easy to read, here's an example:
     *
     *          editId -> id
     *                    editorId
     *                    isEditor     | boolean
     *                    dateNotified | datetime
     *                    dateUnderway | datetime
     *  editorFullName -> fullName
     * editorFirstName -> firstName
     *  editorLastName -> lastName
     *  editorInitials -> initials
     *     editorEmail -> email
     *
     * The most basic line is like the second one. This just means call a getter method `getEditorId()` and put the value
     * into a field called `editorId`. The first line represents a mapping from a getter method that is named different
     * than where how to want to have it. So `editId -> id` calls `getEditId()` and stores the value in `id`. At the
     * end of 3 of these lines are filters, prepended with a pipe. These correspond with functions (see the
     * `apply*Filter()` methods below, that get called on a value before it is stored.
     *
     * There's a script in the plugin `script/formatMapping.php` which will take stdin and output formatted mapping.
     * It's a little clunky because you have to do it on the command line, so there's room for improvement.
     *
     *
     * @param $dataObject
     * @return array
     */
    public static function map($dataObject, $context = null)
    {
        $dataObject = static::preMap($dataObject, $context);

        $out = [];
        $mapping = explode("\n", trim(static::$mapping));
        foreach ($mapping as $mappingConfig) {
            $parts = explode('|', $mappingConfig);
            $fieldConfig = trim(array_shift($parts));

            $trimmed = trim($fieldConfig);

            $fieldNames = explode('->', $trimmed);

            $ours = trim(@$fieldNames[1]);
            $theirs = trim(@$fieldNames[0]);

            if(strlen($ours) === 0) {
                $ours = $theirs;
            }

            if(is_null($context) || self::includeFieldInContext($context, $ours)) {
                // Special handling for source record keys
                if ($ours === self::SOURCE_RECORD_KEY_PROPERTY) {
                    $value = static::getSourceRecordKey($dataObject, $theirs);
                } else {
                    $value = NestedMapper::map(self::getFieldValue($theirs, $dataObject));
                }

                // Process filters that transform values
                if (count($parts)) {
                    foreach ($parts as $filter) {
                        $value = self::applyFilter(trim($filter), $value);
                    }
                }

                // Convert to snakes for JSON
                $out[self::camelToSnake($ours)] = $value;
            }
        }

        return static::postMap($out, $dataObject, $context);
    }

    /**
     * @param $str
     * @return mixed|string
     */
    protected function camelToSnake($str)
    {
        if (empty($str)) {
            return $str;
        }
        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", '_' . "$0", $str);
        return strtolower($str);
    }

    /**
     * Traverses objects / arrays by dot notation
     * @param $fieldName
     * @param $object
     * @throws
     */
    protected static function getFieldValue($key, $object) {
        $currentValue = $object;
        $fieldNameParts = explode('.', $key);
        while(count($fieldNameParts) > 0) {
            $fieldName = array_shift($fieldNameParts);
            if(is_array($currentValue)) {
                $currentValue = $currentValue[$fieldName];
            } elseif(is_object($currentValue)) {
                $methodName = 'get' . ucfirst($fieldName);
                if(method_exists($currentValue, $methodName)) {
                    $currentValue = $currentValue->$methodName();
                } elseif(property_exists($object, $fieldName)) {
                    $currentValue = $currentValue->$fieldName;
                } else {
                    throw new \Exception("Can't get $key from " . get_class($object));
                }
            }
        }
        return $currentValue;
    }

    /**
     * Returns true if we should show the specified field in the specified context
     * @param $context
     * @param $field
     */
    protected static function includeFieldInContext($context, $field)
    {
        if(array_key_exists($context, static::$contexts)) {
            $contextConfiguration = static::$contexts[$context];
            if(array_key_exists('include', $contextConfiguration) && is_array($contextConfiguration['include']) &&
                in_array($field, $contextConfiguration['include'])) return true;


            if(array_key_exists('exclude', $contextConfiguration) && $contextConfiguration['exclude'] == '*' ||
                (is_array($contextConfiguration->exclude) && (in_array($field, $contextConfiguration->exclude ||
                in_array('*', $contextConfiguration['exclude']))))) return false;

            return true;
        }
    }

    /**
     * Use this on the mapper objects to transform the data before the mapping
     * @param $dataObject
     */
    protected static function preMap($dataObject, $context) {
        return $dataObject;
    }


    /**
     * Use this on the mapper objects to transform the data after the mapping
     * @param $out
     * @param $dataObject
     */
    protected static function postMap($out, $dataObject, $context) {
        return $out;
    }

    /**
     * @param $model
     * @return string
     */
    protected static function getSourceRecordKey($model, $theirs = 'id') {
        $method = 'get'.ucfirst($theirs);
        return get_class($model).':'.$model->$method();
    }

    /**
     * @param $filter
     * @param $value
     * @return bool|mixed|string
     * @throws \Exception
     */
    protected static function applyFilter($filter, $value) {
        if($filter === 'boolean') return static::applyBooleanFilter($value);
        if($filter === 'datetime') return static::applyDatetimeFilter($value);

        throw new \Exception("Filter $filter does not exist");
    }

    /**
     * @param $value
     * @return string|null
     * @throws \Exception
     */
    protected static function applyDatetimeFilter($value) {
        if(!is_null($value) && strlen($value) > 0) {
            return DateUtility::formatDateString($value);
        }
        return null;
    }

    /**
     * @param $value
     * @return bool
     */
    protected static function applyBooleanFilter($value) {
        return (bool) $value;
    }
}