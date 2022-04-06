<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use Config;

class Issue extends AbstractDataObjectMapper {
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'title', 'source' => 'localizedTitle'],
        ['property' => 'volume'],
        ['property' => 'number'],
        ['property' => 'year'],
        ['property' => 'published', 'filters' => ['boolean']],
        ['property' => 'current', 'filters' => ['boolean']],
        ['property' => 'datePublished', 'filters' => ['datetime']],
        ['property' => 'description', 'source' => 'localizedTitle'],
        ['property' => 'title', 'source' => 'localizedCoverPageDescription'],
        ['property' => 'coverPageAltText', 'source' => 'localizedCoverPageAltText'],
        ['property' => 'width', 'source' => 'issueWidth'],
        ['property' => 'height', 'source' => 'issueHeight'],
        ['property' => 'articlesCount', 'source' => 'numArticles'],
        ['property' => 'issueFileName', 'source' => 'localizedFileName'],
        ['property' => 'originalFileName', 'source' => 'localizedOriginalFileName'],
    ];
}