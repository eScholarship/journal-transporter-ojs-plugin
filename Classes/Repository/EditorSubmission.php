<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class EditorSubmission {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'editorSubmission';

    /**
     * @param $article
     * @param null $round
     * @return mixed
     */
    public function fetchEditorDecisionsByArticle($article, $round = null) {
        return $this->getEditorDecisions($article->getId(), $round);
    }
}