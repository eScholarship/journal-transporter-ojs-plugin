<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class Section extends AbstractDataObjectMapper {
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title']]];

    protected static $mapping = <<<EOF
		             id -> sourceRecordKey
		 localizedTitle -> title
		localizedAbbrev -> abbreviation
		                   sequence
EOF;
}
