<?php namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObjectUtility;
use JournalTransporterPlugin\Utility\DateUtility;
use JournalTransporterPlugin\Utility\SourceRecordKeyUtility;

class Rounds extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $sectionEditorSubmissionRepository;
    protected $editAssignmentRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $numberOfRounds = $this->sectionEditorSubmissionRepository->fetchNumberOfRoundsByArticle($article);
        $round = (int)$parameters['round'];

        if ($round) {
            if ($round < 1 || $round > $numberOfRounds) throw new \Exception("Round $round doesn't exist");
            return $this->getRound($article, $round);
        } else {
            return $this->getRounds($article, $numberOfRounds);
        }
    }

    /**
     * @param $article
     * @param $numberOfRounds
     * @return array
     */
    protected function getRounds($article, $numberOfRounds)
    {
        $out = [];
        for($i = 1; $i <= $numberOfRounds; $i++) {
            $out[] = (object)['source_record_key' => SourceRecordKeyUtility::round($article->getId(), $i)];
        }
        return $out;
    }

    /**
     * @param $article
     * @param $round
     * @return array
     *
     */
    protected function getRound($article, $round)
    {
        // These are ordered ASC by date
        $assignments = $this->editAssignmentRepository->fetchByArticle($article)->toArray();
        $dateUnderway = null;
        if(count($assignments)) {
            $dateUnderway = $assignments[0]->getDateUnderway();
        }

        return (object)[
            'source_record_key' => SourceRecordKeyUtility::round($article->getId(), $round),
            'round' => $round,
            'date' => DateUtility::formatDateString( $dateUnderway ?: $article->getLastModified())
        ];
    }
}