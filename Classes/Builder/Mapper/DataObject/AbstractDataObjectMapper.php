<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Utility\Date;
use JournalTransporterPlugin\Utility\Enums\Role;
use JournalTransporterPlugin\Utility\Enums\CommentType;
use JournalTransporterPlugin\Utility\SourceRecordKey;
use JournalTransporterPlugin\Exception\InvalidMappingConfigurationException;
use JournalTransporterPlugin\Exception\InvalidArgumentException;

class AbstractDataObjectMapper {

    /**
     * Where we store record identifiers in the JSON we output
     */
    const SOURCE_RECORD_KEY_PROPERTY = 'sourceRecordKey';

    const CAMEL_TO_SNAKE_EXCEPTIONS = ['iPaddress' => 'ip_address'];

    const ON_ERROR_TRIGGER_EXCEPTION = '!@#$!@#$';

    /** @var array  */
    protected static $contexts = [];

    /**
     * All subclasses have these contexts
     * @var array[]
     */
    protected static $sharedContexts = ['sourceRecordKey' => ['exclude' => '*', 'include' => ['sourceRecordKey']]];

    /**
     * Extend this class, and name the child class after a OJS class. Add a static parameter called $mapping, which
     * is an array. Each item of the array contains a field mapping.
     *
     * @param $dataObject
     * @return array
     */
    public static function map($dataObject, $context = null)
    {
        $dataObject = static::preMap($dataObject, $context);

        $out = [];
        foreach (static::$mapping as $mappingConfig) {
            $property = trim($mappingConfig['property']);
            $source = trim(@$mappingConfig['source']);

            $onError = array_key_exists('onError', $mappingConfig) ? $mappingConfig['onError'] : self::ON_ERROR_TRIGGER_EXCEPTION;
            $internalContext = array_key_exists('context', $mappingConfig) ? $mappingConfig['context'] : null;

            if(strlen($source) === 0) {
                $source = $property;
            }
            if(is_null($context) || self::includeFieldInContext($context, $property)) {
                // Special handling for source record keys
                if ($property === self::SOURCE_RECORD_KEY_PROPERTY) {
                    $value = static::getSourceRecordKey($dataObject, $source);
                } else {
                    $value = NestedMapper::map(self::getFieldValue($source, $dataObject, $onError), $internalContext);
                }

                // Turns an id into a source record key object
                if(array_key_exists('sourceRecordKey', $mappingConfig)) {
                    $value = self::toSourceRecordKey($mappingConfig['sourceRecordKey'], $value);
                }

                // Turns an enum value into a label
                if(array_key_exists('mapTo', $mappingConfig)) {
                    if($mappingConfig['mapTo'] == 'role') $value = Role::getRoleName($value);
                    if($mappingConfig['mapTo'] == 'commentType') $value = CommentType::getCommentTypeName($value);

                }

                // Process filters that transform values
                if (array_key_exists('filters', $mappingConfig)) {
                    foreach ($mappingConfig['filters'] as $filter) {
                        $value = self::applyFilter(trim($filter), $value);
                    }
                }

                // Convert to snakes for JSON
                $out[self::camelToSnake($property)] = $value;
            }
        }
        return static::postMap($out, $dataObject, $context);
    }

    /**
     * @param $str
     * @return mixed|string
     */
    protected static function camelToSnake($str)
    {
        if(array_key_exists($str, self::CAMEL_TO_SNAKE_EXCEPTIONS)) return self::CAMEL_TO_SNAKE_EXCEPTIONS[$str];

        if (empty($str)) {
            return $str;
        }
        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", '_' . "$0", $str);
        return strtolower($str);
    }

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    protected static function toSourceRecordKey($type, $id)
    {
        if(method_exists(SourceRecordKey::class, $type))
            return (object) ['source_record_key' => SourceRecordKey::$type($id)];
        throw new InvalidArgumentException("Can't generate source record key for $type");
    }

    /**
     * Traverses objects / arrays by dot notation
     * @param $fieldName
     * @param $object
     * @param $onError The value to return if there's an error, defaults to except via cheap mechanism
     * @throws
     */
    protected static function getFieldValue($key, $object, $onError = self::ON_ERROR_TRIGGER_EXCEPTION) {
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
                    if($onError !== self::ON_ERROR_TRIGGER_EXCEPTION) {
                        return $onError;
                    }
                    throw new InvalidMappingConfigurationException("Can't get \"$key\" from " . get_class($object));
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
        $mergedContexts = array_merge(self::$sharedContexts, static::$contexts);
        if(array_key_exists($context, $mergedContexts)) {
            $contextConfiguration = $mergedContexts[$context];
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
        if(get_class($model) === 'stdClass') list($class, $id) = [$model->__mapperClass, $model->$theirs];
        else {
            $method = 'get'.ucfirst($theirs);
            list($class, $id) = [get_class($model), $model->$method()];
        }
        return $class.':'.$id;
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
        if($filter === 'integer') return static::applyIntegerFilter($value);
        if($filter === 'html') return static::applyHTMLFilter($value);

        throw new InvalidMappingConfigurationException("Filter $filter does not exist");
    }

    /**
     * @param $value
     * @return string|null
     * @throws \Exception
     */
    protected static function applyDatetimeFilter($value) {
        if(!is_null($value) && strlen($value) > 0) {
            return Date::formatDateString($value);
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

    /**
     * @param $value
     * @return bool
     */
    protected static function applyIntegerFilter($value) {
        return (int) $value;
    }

    /**
     * @param $value
     * @return string
     */
    protected static function applyHTMLFilter($value) {
        $steps = [$value];
        $steps[] = strip_tags(end($steps), '<p><ul><li><ol><em><i><strong>');
        // A bullet-vulnerable way to strip attributes off of HTML tags
        $steps[] = preg_replace('/(<[a-zA-Z]+)[^>]*>/','\1>', end($steps));
        return end($steps);
    }
}