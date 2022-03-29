<?php namespace CdlExportPlugin\Api\Journals;

use CdlExportPlugin\Builder\Mapper\NestedMapper;
use CdlExportPlugin\Utility\DataObjectUtility;
use CdlExportPlugin\Api\ApiRoute;

class Articles extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        return @$parameters['article'] ?
            $this->getArticle($parameters['article'], $journal, $arguments[ApiRoute::DEBUG_ARGUMENT]) :
            $this->getArticles($journal);
    }

    /**
     * @param $journal
     * @return array
     */
    protected function getArticles($journal)
    {
        $resultSet = $this->articleRepository->fetchByJournal($journal);
        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $resultSet->toArray());
    }

    /**
     * @param $id
     * @param $journal
     * @return array
     */
    protected function getArticle($id, $journal, $debug)
    {
        $article = $this->articleRepository->fetchByIdAndJournal($id, $journal);
        if($debug) return DataObjectUtility::dataObjectToArray($article);

        $history = new \CdlExportPlugin\Builder\History($article);
        $dates = [
            'date_started' => $history->getMilestoneDate('submission.event.general.articleSubmitted'),
            'date_accepted' => $history->getMilestoneDate('submission.event.editor.editorDecision', '/\(Accept Submission\)/'),
            'date_declined' => $history->getMilestoneDate('submission.event.editor.editorDecision', '/\(Decline Submission\)/'),
            'date_submitted' => $history->getMilestoneDate('submission.event.general.articleSubmitted'),
            'date_published' => $history->getMilestoneDate('submission.event.general.articlePublished'),
            'date_updated' => $history->getMilestoneDate('submission.event.general.metadataUpdated')
        ];

        $mappedArticle = NestedMapper::map($article);

        return array_merge($mappedArticle, $dates);
    }
}