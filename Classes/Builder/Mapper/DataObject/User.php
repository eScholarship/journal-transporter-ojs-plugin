<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class User extends AbstractDataObjectMapper {
    protected static $contexts = ['roles' => ['exclude' => '*', 'include' => ['sourceRecordKey']]];

    protected static $mapping = <<<EOF
		                    userId -> sourceRecordKey
		                              firstName
		                              middleName
		                              lastName
		                              initials
		      localizedAffiliation -> affiliation
		                              salutation
		                              gender
		                              email
		                              url
		                              phone
		                              fax
		                              mailingAddress
		                              country
		        localizedBiography -> biography
		                              interests
		localizedProfessionalTitle -> professionalTitle
		          contactSignature -> signature  
EOF;
}