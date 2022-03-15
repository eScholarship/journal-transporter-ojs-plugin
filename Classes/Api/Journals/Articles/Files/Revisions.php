<?php namespace CdlExportPlugin\Api\Journals\Articles\Files;

use CdlExportPlugin\Builder\Mapper\NestedMapper;
use CdlExportPlugin\Api\ApiRoute;

class Revisions extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $fileRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $file = $this->fileRepository->fetchById($parameters['file']);
        $revisions = $this->fileRepository->fetchRevisionsByFile($file);
        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $revisions);
    }

}