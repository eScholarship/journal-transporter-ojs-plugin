<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class User extends AbstractDataObjectMapper {
    protected static $contexts = ['roles' => ['exclude' => '*', 'include' => ['sourceRecordKey']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'userId'],
        ['property' => 'firstName'],
        ['property' => 'middleName'],
        ['property' => 'lastName'],
        ['property' => 'initials'],
        ['property' => 'affiliation', 'source' => 'localizedAffiliation'],
        ['property' => 'salutation'],
        ['property' => 'gender'],
        ['property' => 'email'],
        ['property' => 'url'],
        ['property' => 'phone'],
        ['property' => 'fax'],
        ['property' => 'mailingAddress'],
        ['property' => 'countryCode', 'source' => 'country'],
        ['property' => 'biography', 'source' => 'localizedBiography'],
        ['property' => 'interests'],
        ['property' => 'title', 'source' => 'localizedProfessionalTitle'],
        ['property' => 'signature', 'source' => 'contactSignature'],
    ];
}