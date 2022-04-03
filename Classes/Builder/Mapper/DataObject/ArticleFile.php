<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class ArticleFile  extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'fileId'],
        ['property' => 'fileId'],
        ['property' => 'filePath'],
        ['property' => 'fileName'],
        ['property' => 'fileType'],
        ['property' => 'originalFilename'],
        ['property' => 'dateUploaded', 'filters' => ['datetime']],
    ];

    /**
     * @param $model
     * @return string
     */
    protected static function getSourceRecordKey($model) {
        return get_class($model).':'.$model->getArticleId().':'.$model->getFileId();
    }
}