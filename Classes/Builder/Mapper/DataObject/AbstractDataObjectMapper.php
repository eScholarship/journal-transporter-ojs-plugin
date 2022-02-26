<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use CdlExportPlugin\Builder\Mapper\NestedMapper;

class AbstractDataObjectMapper {

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
        $out = [];
        $mapping = explode("\n", trim(static::$mapping));
        foreach ($mapping as $mappingConfig) {
            $parts = explode('|', $mappingConfig);
            $fieldConfig = trim(array_shift($parts));

            $trimmed = trim($fieldConfig);

            list($theirs, $ours) = explode('->', $trimmed);

            $ours = trim($ours);
            $theirs = trim($theirs);

            if(strlen($ours) === 0) {
                $ours = $theirs;
            }

            if(is_null($context) || self::includeFieldInContext($context, $ours)) {
                $methodName = 'get' . ucfirst($theirs);

                // Special handling for all local ids
                if ($ours === 'id') {
                    $value = static::getSystemId($dataObject, $theirs);
                } else {
                    $value = NestedMapper::map($dataObject->$methodName());
                }

                // Process filters
                if (count($parts)) {
                    foreach ($parts as $filter) {
                        $value = self::applyFilter(trim($filter), $value);
                    }
                }

                $out[$ours] = $value;
            }
        }

        return static::postMap($out, $dataObject, $context);
    }

    /**
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
     * Use this on the mapper objects to transform the data after the mapping
     * @param $out
     * @param $dataObject
     */
    protected static function postMap($out, $dataObject) {
        return $out;
    }

    /**
     * @param $model
     * @return string
     */
    protected static function getSystemId($model, $theirs = 'id') {
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
            return (new \DateTime($value, new \DateTimeZone('America/Los_Angeles')))
                ->format('c');
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