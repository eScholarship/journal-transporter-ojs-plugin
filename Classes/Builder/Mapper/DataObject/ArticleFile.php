<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Repository\File;
use JournalTransporterPlugin\Repository\SupplementaryFile;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\GalleyFile;

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
        ['property' => 'round', 'filters' => ['integer']],
        ['property' => 'parentSourceRecordKey', 'onError' => null],
        ['property' => 'isGalleyFile', 'onError' => false],
        ['property' => 'isSupplementaryFile', 'onError' => false],
        ['property' => 'title', 'source' => 'settings.title', 'onError' => null],
        ['property' => 'description', 'source' => 'settings.description', 'onError' => null],
        ['property' => 'creator', 'source' => 'settings.creator', 'onError' => null],
        ['property' => 'publisher', 'source' => 'settings.publisher', 'onError' => null],
        ['property' => 'source', 'source' => 'settings.source', 'onError' => null],
        ['property' => 'typeOther', 'source' => 'settings.typeOther', 'onError' => null],
    ];

    /**
     * @param $dataObject
     * @param $context
     * @return mixed
     */
    protected static function preMap($dataObject, $context) {
        if(!is_null($dataObject->getRevision())) $dataObject->parentSourceRecordKey = self::getParentSourceRecordKey($dataObject);

        $associatedFile = self::getAssociatedFileRecord($dataObject);
        $textFields = ['title', 'description', 'creator', 'publisher', 'source', 'typeOther'];
        if(is_object($associatedFile)) {
            if(method_exists($associatedFile, 'getLocalizedData')) {
                $settings = [];
                foreach($textFields as $field) {
                    self::$mapping[] = ['property' => $field, 'source' => "settings.$field", 'onError' => null];
                    $settings[$field] = $associatedFile->getLocalizedData($field);
                }
                $dataObject->settings = $settings;
            }

            // Add some boolean flags indicating the type of file based on the associatedFile record type
            $dataObject->isGalleyFile = in_array(
                get_class($associatedFile),
                [ \ArticleGalley::class, \ArticleHTMLGalley::class ]
            );
            $dataObject->isSupplementaryFile = get_class($associatedFile) === \SuppFile::class;
        }

        // Here's where we would define the $textFields for ArticleFiles that are not supps or galleys
        // TODO: at some point, we might want to set values programatically for title, description, creator, etc.
        if(!property_exists($dataObject, 'settings')) {
            $dataObject->settings = [ 'title' => ''];
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
    protected function getAssociatedFileRecord($articleFile) {
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