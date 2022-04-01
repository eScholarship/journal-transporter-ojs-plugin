<?php namespace CdlExportPlugin\Api\Journals\Articles;

use CdlExportPlugin\Builder\Mapper\NestedMapper;
use CdlExportPlugin\Api\ApiRoute;
use CdlExportPlugin\Utility\DataObjectUtility;

class Files extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $fileRepository;
    protected $galleyFileRepository;
    protected $supplementaryFileRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);

        $files = $this->getFilesByType($parameters['fileType'], $article);

        if($arguments[ApiRoute::DEBUG_ARGUMENT]) return DataObjectUtility::dataObjectToArray($files);

        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $files);
    }

    /**
     * @param $type
     * @param $article
     * @return mixed
     * @throws \Exception
     */
    protected function getFilesByType($type, $article)
    {
        switch($type) {
            case 'galley':
                $files = $this->galleyFileRepository->fetchByArticle($article);
                break;
            case 'supplementary':
                $files = $this->supplementaryFileRepository->fetchByArticle($article);
                break;
            case 'article':
                $files = $this->fileRepository->fetchByArticle($article);
                break;
            default:
                throw new \Exception('Invalid file type requested in '.__METHOD__);
        }
        return $files;
    }
}