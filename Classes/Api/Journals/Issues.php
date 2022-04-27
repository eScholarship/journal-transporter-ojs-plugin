<?php namespace JournalTransporterPlugin\Api\Journals;

use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Utility\DataObjectUtility;

class Issues extends ApiRoute  {
    protected $journalRepository;
    protected $issueRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        if(@$parameters['issue']) {
            return $this->getIssue($parameters['issue'], $parameters['journal'], $arguments[ApiRoute::DEBUG_ARGUMENT]);
        } else {
            return $this->getIssues($parameters);
        }
    }

    protected function getIssue($issueId, $journalId, $debug)
    {
        $journal = $this->journalRepository->fetchOneById($journalId);
        $item = $this->issueRepository->fetchByIdAndJournal($issueId, $journal);

        // Determine the published order -- probably could be improved
        $sequence = null;
        if($item->getPublished()) {
            $i = 0;
            $publishedIssues = $this->issueRepository->fetchPublishedByJournal($journal)->toArray();
            foreach($publishedIssues as $publishedIssue) {
                if($publishedIssue->getId() == $item->getId()) {
                    $sequence = $i;
                    break;
                }
                $i++;
            }
        }

        if($debug) return DataObjectUtility::dataObjectToArray($item);
        $issue = NestedMapper::map($item);
        $issue['sequence'] = $sequence;
        return $issue;
    }


    /**
     * @param $parameters
     * @return array
     */
    public function getIssues($parameters)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $publishedIssues = $this->issueRepository->fetchPublishedByJournal($journal)->toArray();
        $unpublishedIssues = $this->issueRepository->fetchUnpublishedByJournal($journal)->toArray();

        $allIssues = array_merge($publishedIssues, $unpublishedIssues);

        $iterator = 0;
        $issues = [];
        foreach($allIssues as $item) {
            $issue = NestedMapper::map($item, 'index');
            $issue['sequence'] = $item->getPublished() ? $iterator++ : null;
            $issues[] = $issue;
        }
        return $issues;
    }
}