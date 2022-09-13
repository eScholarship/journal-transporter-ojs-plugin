<?php namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObject;

class StateTransitions extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $signoffRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);

        $signoffs = $this->signoffRepository->fetchByArticle($article)->toArray();

        if($arguments[ApiRoute::DEBUG_ARGUMENT]) return DataObject::dataObjectToArray($signoffs);
        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $signoffs);
    }

}