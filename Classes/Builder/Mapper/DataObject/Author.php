<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Repository\User;

class Author extends AbstractDataObjectMapper
{
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'authorId'],
        ['property' => 'user', 'sourceRecordKey' => 'user'],
        ['property' => 'firstName'],
        ['property' => 'middleName'],
        ['property' => 'lastName'],
        ['property' => 'initials'],
        ['property' => 'affiliation', 'source' => 'localizedAffiliation'],
        ['property' => 'salutation'],
        ['property' => 'country'],
        ['property' => 'url'],
        ['property' => 'email'],
        ['property' => 'biography', 'source' => 'localizedBiography'],
        ['property' => 'sequence', 'filters' => ['integer']],
        ['property' => 'primaryContact', 'filters' => ['boolean']],
    ];

    /**
     * @param $dataObject
     * @param $context
     * @return mixed
     */
    protected static function preMap($dataObject, $context)
    {
        $user = (new User)->getUserByEmail($dataObject->getEmail());
        $dataObject->user = is_object($user) ? $user->getId() : null;
        return $dataObject;
    }
}