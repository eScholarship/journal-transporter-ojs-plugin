<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class EditAssignment extends AbstractDataObjectMapper {
    protected static $mapping = <<<EOF
		         editId -> sourceRecordKey
		                   editorId
		                   isEditor     | boolean
		                   dateNotified | datetime
		                   dateUnderway | datetime
		 editorFullName -> fullName
		editorFirstName -> firstName
		 editorLastName -> lastName
		 editorInitials -> initials
		    editorEmail -> email
EOF;
}