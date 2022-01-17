<?php namespace CdlExportPlugin\Api\Journals;

use CdlExportPlugin\Repository\Journal;
use CdlExportPlugin\Repository\Issue;

class Issues {
    private $journalRepository;
    private $issueRepository;

    public function __construct()
    {
        $this->journalRepository = new Journal;
        $this->issueRepository = new Issue;
    }

    /**
     * @param array $args
     * @return array
     * @throws \Exception
     */
    public function execute($args)
    {
        $journal = $this->journalRepository->fetchOneById($args['journal']);
        $issuesResultSet = $this->issueRepository->fetchByJournal($journal);

        $issues = [];
        foreach ($issuesResultSet->toArray() as $issue) {
            $issues[] = [
                'title'                => $issue->getLocalizedTitle(),
                'id'                   => $issue->getId(),
                'volume'               => $issue->getVolume(),
                'number'               => $issue->getNumber(),
                'year'                 => $issue->getYear(),
                'published'            => (bool) $issue->getPublished(),
                'current'              => (bool) $issue->getCurrent(),
                'datePublished'        => $issue->getDatePublished(),
                'coverPageDescription' => $issue->getLocalizedCoverPageDescription(),
                'coverPageAltText'     => $issue->getLocalizedCoverPageAltText(),
                'width'                => $issue->getIssueWidth(),
                'height'               => $issue->getIssueHeight(),
                'articlesCount'        => $issue->getNumArticles(),
                'issueFileName'        => $issue->getLocalizedFileName(),
                'originalFileName'     => $issue->getLocalizedOriginalFileName(),
            ];
        }

        return $issues;
    }
}