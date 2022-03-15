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
		                              countryCode
		        localizedBiography -> biography
		                              interests
		localizedProfessionalTitle -> title
		          contactSignature -> signature  
EOF;
}