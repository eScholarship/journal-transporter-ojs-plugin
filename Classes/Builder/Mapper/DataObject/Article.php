<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use CdlExportPlugin\Repository\AuthorSubmission;

class Article extends AbstractDataObjectMapper {
    protected static $mapping = <<<EOF
		                               id -> sourceRecordKey
		                     sectionTitle -> section
		                     articleTitle -> title
		                                     authors
		                                     language
        authorSubmission.submissionStatus -> submissionStatus
EOF;

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function preMap($dataObject, $context) {
        // Add the authorSubmission onto the article -- it has some useful info not on the article
        $dataObject->authorSubmission = (new AuthorSubmission)->fetchByArticle($dataObject);
        return $dataObject;
    }

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function postMap($data, $dataObject, $context) {
        $data['status'] = self::mapJournalStatus($dataObject->getStatus());

        $data['galleys'] = [];
        foreach($dataObject->authorSubmission->getGalleys() as $galley) {
            $data['galleys'][] = ['file_id' => $galley->getFileId()];
        }

        return $data;
    }

    /**
     * @param $status
     */
    protected static function mapJournalStatus($status) {
        // TODO: this mapping probably isn't quite right; also this mapping should probably go somewhere else
        // TODO: also this is a weird construction I've made here
        // TODO: also we're missing some stages: copyediting, typesetting, proofing, maybe others
        return @[STATUS_ARCHIVED => 'archived',
                 STATUS_QUEUED => 'submitted',
                 STATUS_PUBLISHED => 'published',
                 STATUS_DECLINED => 'rejected',
                 STATUS_QUEUED_UNASSIGNED => 'submitted',
                 STATUS_QUEUED_REVIEW => 'review',
                 STATUS_QUEUED_EDITING => 'copyediting',
                 STATUS_INCOMPLETE => 'draft']
                 [$status] ?: null;

    }
}