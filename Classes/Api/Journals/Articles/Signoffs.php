<?php namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObject;

class Signoffs extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $copyeditorSubmissionRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $copyeditorSubmission = $this->copyeditorSubmissionRepository->fetchByArticle($article);
        print_r($copyeditorSubmission); die();
        return DataObject::dataObjectToArray($copyeditorSubmission);


//        if($arguments[ApiRoute::DEBUG_ARGUMENT]) return DataObject::dataObjectToArray($files);
//        return array_map(function($item) {
//            return NestedMapper::map($item);
//        }, $files);
    }

}