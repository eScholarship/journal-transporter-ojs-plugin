<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class ArticleEventLogEntry extends AbstractDataObjectMapper
{
    protected static $mapping = <<<EOF
		    logId -> sourceRecordKey
		             articleId
		             userId
		             dateLogged          | datetime
		iPaddress -> ip
		             logLevel
		             eventType
		             assocType
		             message
		             logLevelString
		             eventTitle
		             userFullName
		             userEmail
		             assocTypeString
		             assocTypeLongString
EOF;
}