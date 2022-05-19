<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class SectionEditorSubmission {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'sectionEditorSubmission';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getSectionEditorSubmission($article->getId());
    }

    /**
     * @param $article
     * @return mixed
     */
    public function fetchNumberOfRoundsByArticle($article)
    {
        return $this->getMaxReviewRound($article->getId());
    }

    /**
     * @param $article
     * @param null $round
     * @return mixed
     */
    public function fetchEditorDecisionsByArticle($article, $round = null)
    {
        $decisions = $this->getEditorDecisions($article->getId(), $round);
        // It would be nice if we didn't have to do this, but OJS doesn't return the round in this array of decisions
        if($round !== null) {
            foreach($decisions as &$decision) $decision['round'] = $round;
        }
        return $decisions;
    }
}