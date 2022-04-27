<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

class Author extends AbstractDataObjectMapper
{
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'authorId'],
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
}