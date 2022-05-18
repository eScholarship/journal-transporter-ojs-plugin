<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class EditorSubmission {
    public function fetchEditorDecisionsByArticle($article, $round = null) {
        return DAOFactory::get()->getDAO('editorSubmission')->getEditorDecisions($article->getId(), $round);
    }
}