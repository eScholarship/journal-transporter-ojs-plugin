<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use Config;

class Issue extends AbstractDataObjectMapper {
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title']]];

    protected static $mapping = <<<EOF
                                   id -> sourceRecordKey
		               localizedTitle -> title
		                                 volume
		                                 number
		                                 year
		                                 published            | boolean
		                                 current              | boolean
		                                 datePublished        | datetime
		localizedCoverPageDescription -> coverPageDescription
		    localizedCoverPageAltText -> coverPageAltText
		                   issueWidth -> width
		                  issueHeight -> height
		                  numArticles -> articlesCount
		            localizedFileName -> issueFileName
		    localizedOriginalFileName -> originalFileName
EOF;
}