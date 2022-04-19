<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use CdlExportPlugin\Repository\File;
use CdlExportPlugin\Repository\SupplementaryFile;
use CdlExportPlugin\Repository\Article;
use CdlExportPlugin\Repository\GalleyFile;

class ArticleFile  extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'fileId'],
        ['property' => 'fileId'],
        ['property' => 'filePath'],
        ['property' => 'fileName'],
        ['property' => 'fileType'],
        ['property' => 'originalFilename'],
        ['property' => 'dateUploaded', 'filters' => ['datetime']],
        ['property' => 'type'],
        ['property' => 'parentSourceRecordKey', 'onError' => null],
        ['property' => 'isGalleyFile', 'onError' => false],
        ['property' => 'isSupplementaryFile', 'onError' => false],
    ];

    /**
     * @param $dataObject
     * @param $context
     * @return mixed
     */
    protected static function preMap($dataObject, $context) {
        if(!is_null($dataObject->getRevision())) $dataObject->parentSourceRecordKey = self::getParentSourceRecordKey($dataObject);

        $specialFile = self::getSpecialFile($dataObject);
        if(is_object($specialFile)) {
            $dataObject->isGalleyFile = in_array(
                get_class($specialFile),
                [ \ArticleGalley::class, \ArticleHTMLGalley::class ]
            );
            $dataObject->isSupplementaryFile = get_class($specialFile) === \SuppFile::class;
        }

        return $dataObject;
    }

    /**
     * Gets associated galley or supplementary files. There might be more efficient ways to do this, but I used this
     * approach to resolve a tight coupling between this class and the Article/File controller.
     *
     * @param $article
     * @param $articleFiles
     * @return mixed
     */
    protected function getSpecialFile($articleFile) {
        $article = (new Article)->fetchById($articleFile->getArticleId());

        // Get the other file types, all of which are also article files
        $specialFileGroups = [
            (new GalleyFile)->fetchByArticle($article),
            (new SupplementaryFile)->fetchByArticle($article),
        ];
        $specialFiles = array_merge(...array_filter($specialFileGroups));
        foreach($specialFiles as $specialFile) {

            // File names are unique, in theory
            if(basename($specialFile->getFilePath()) === basename($articleFile->getFilePath())) return $specialFile;
        }
        return null;
    }


    /**
     * @param $model
     * @return string
     */
    protected static function getSourceRecordKey($model) {
        return self::generateSourceRecordKey(get_class($model), $model->getArticleId(), $model->getFileId(), $model->getRevision());
    }

    /**
     * @param $dataObject
     * @return string
     */
    protected static function getParentSourceRecordKey($dataObject) {
        $revision = $dataObject->getRevision();
        if($revision <= 1) return null;

        $revisions = (new File)->fetchFileRevisionsUpTo($dataObject, $revision - 1);
        $firstRevision = array_pop($revisions);

        if(is_null($firstRevision)) return null;

        return self::generateSourceRecordKey(get_class($dataObject), $dataObject->getArticleId(), $dataObject->getFileId(), $firstRevision->getRevision());
    }

    /**
     * @param $model
     * @return string
     */
    protected static function generateSourceRecordKey($modelName, $articleId, $fileId, $revision) {
        return $modelName.':'.$articleId.':'.$fileId.'-'.($revision ?: '0');
    }
}