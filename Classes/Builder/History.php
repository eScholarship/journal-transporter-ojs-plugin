<?php namespace CdlExportPlugin\Builder;

use CdlExportPlugin\Builder\Mapper\NestedMapper;
use CdlExportPlugin\Utility\DAOFactory;
use CdlExportPlugin\Utility\DataObjectUtility;

class History
{
    protected $article;

    protected $events = [];

    public function __construct($article) {
        $this->article = $article;
        $this->build();
    }

    public function toArray() {
        return $this->events;
    }

    protected function build() {
        $this->logEntries();
    }

    protected function logEntries() {
        $articleLogEntries = DAOFactory::get()->getDAO('articleEventLog')
            ->getArticleLogEntries($this->article->getId())->toArray();

        usort($articleLogEntries, function($a, $b) {
            return (new \DateTime($a->getDateLogged()) > (new \DateTime($b->getDateLogged())));
        });

        foreach($articleLogEntries as $articleLogEntry) {
            $associatedObject = $this->getAssociatedObject($articleLogEntry);

            $this->append([
                'articleLogEntry' => NestedMapper::map($articleLogEntry),
                'associatedObject' => NestedMapper::map($associatedObject)
            ]);
        }
    }

    protected function getAssociatedObject($articleLogEntry) {
        switch($articleLogEntry->getAssocType()) {
            case ARTICLE_LOG_TYPE_DEFAULT:

            case ARTICLE_LOG_TYPE_AUTHOR:
            case ARTICLE_LOG_TYPE_EDITOR:
                $dao = 'user';
                $method = 'getUser';
                break;
            break;
            case ARTICLE_LOG_TYPE_REVIEW:
                $dao = 'reviewAssignment';
                $method = 'getReviewAssignmentById';
            case ARTICLE_LOG_TYPE_COPYEDIT:
                // CopyeditAssignment
            case ARTICLE_LOG_TYPE_LAYOUT:
                // LayoutAssignment
            case ARTICLE_LOG_TYPE_PROOFREAD:
                // ProofreadAssignment
        }

        if($dao and $method) {
            return DAOFactory::get()->getDAO($dao)->$method($articleLogEntry->getAssocId());
        }
        return null;
    }

    protected function append($event) {
        $this->events[] = $event;
    }
}