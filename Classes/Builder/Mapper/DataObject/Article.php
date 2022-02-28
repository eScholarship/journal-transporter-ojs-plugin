<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class Article extends AbstractDataObjectMapper {
    protected static $mapping = <<<EOF
		          id -> sourceRecordKey
		sectionTitle -> section
		articleTitle -> title
		                authors
		                statusKey
EOF;
}