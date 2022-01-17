<?php namespace CdlExportPlugin\Api\Journals;

use CdlExportPlugin\Repository\Article;
use CdlExportPlugin\Repository\Journal;
use CdlExportPlugin\Utility\DataObjectUtility;

class Articles {
    private $journalRepository;
    private $articleRepository;

    public function __construct()
    {
        $this->journalRepository = new Journal;
        $this->articleRepository = new Article;
    }

    /**
     * @param array $args
     * @return array
     * @throws \Exception
     */
    public function execute($args)
    {
        $journal = $this->journalRepository->fetchOneById($args['journal']);
        return @$args['article'] ? $this->getArticle($args['article'], $journal) : $this->getArticles($journal);
    }

    protected function getArticles($journal)
    {
        $articleResultSet = $this->articleRepository->fetchByJournal($journal);

        $issues = [];
        foreach ($articleResultSet->toArray() as $issue) {
            $issues[] = [
                'title'        => $issue->getLocalizedTitle(),
                'id'           => $issue->getId(),
                'sectionId'    => $issue->getSectionId(),
                'sectionTitle' => $issue->getSectionTitle()
            ];
        }

        return $issues;
    }

    protected function getArticle($id, $journal)
    {
        $article = $this->articleRepository->fetchByIdAndJournal($id, $journal);
        return DataObjectUtility::dataObjectToArray($article);
    }
}