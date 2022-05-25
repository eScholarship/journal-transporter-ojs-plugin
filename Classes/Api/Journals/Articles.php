<?php namespace JournalTransporterPlugin\Api\Journals;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Utility\DataObject;
use JournalTransporterPlugin\Api\ApiRoute;

class Articles extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $authorSubmissionRepository;
    protected $publishedArticleRepository;

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
            return NestedMapper::map($item, 'index');
        }, $resultSet->toArray());
    }

    /**
     * @param $id
     * @param $journal
     * @param $debug
     * @return array
     */
    protected function getArticle($id, $journal, $debug)
    {
        $article = $this->articleRepository->fetchByIdAndJournal($id, $journal);
        if($debug) return $this->getDebugResponse($article);
        return NestedMapper::map($article);
    }

    /**
     * @param $article
     * @return object
     */
    protected function getDebugResponse($article) {
        return (object) [
            'article' => DataObject::dataObjectToArray($article),
            'authorSubmission' => DataObject::dataObjectToArray(
                $this->authorSubmissionRepository->fetchByArticle($article)
            ),
            'editorDecisions' => DataObject::dataObjectToArray(
                $this->authorSubmissionRepository->fetchEditorDecisionsByArticle($article)
            )
        ];
    }
}