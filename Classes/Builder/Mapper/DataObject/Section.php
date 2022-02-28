<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class Section extends AbstractDataObjectMapper {
    protected static $mapping = <<<EOF
		             id -> sourceRecordKey
		 localizedTitle -> title
		localizedAbbrev -> abbreviation
EOF;
}
