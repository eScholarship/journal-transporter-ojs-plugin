<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class ArticleFile  extends AbstractDataObjectMapper {
    protected static $mapping = <<<EOF
		fileId -> sourceRecordKey
		          filePath
		          fileName
		          fileType
		          originalFilename
		          dateUploaded
EOF;

    /**
     * @param $model
     * @return string
     */
    protected static function getSourceRecordKey($model) {
        return get_class($model).':'.$model->getArticleId().':'.$model->getFileId();
    }
}