<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class SectionEditorSubmission {
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('sectionEditorSubmission')->getSectionEditorSubmission($article->getId());
    }

    public function fetchNumberOfRoundsByArticle($article)
    {
        return DAOFactory::get()->getDAO('sectionEditorSubmission')->getMaxReviewRound($article->getId());
    }

    public function fetchEditorDecisionsByArticle($article, $round = null)
    {
        $decisions = DAOFactory::get()->getDAO('editorSubmission')->getEditorDecisions($article->getId(), $round);
        // It would be nice if we didn't have to do this, but OJS doesn't return the round in this array of decisions
        if($round !== null) {
            foreach($decisions as &$decision) $decision['round'] = $round;
        }
        return $decisions;
    }
}