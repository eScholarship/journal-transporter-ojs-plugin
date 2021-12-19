<?php namespace CdlExportPlugin\Command\Journals;

use CdlExportPlugin\Command\Traits\Handler;
use CdlExportPlugin\Utility\DataObjectUtility;
use CdlExportPlugin\Utility\Traits\DAOInjection;

class Journal {
    use Handler;
    use DAOInjection;

    private $DAOs = ['journal', 'issue', 'section'];

    private $journal;

    public function __construct($args) {
        $this->initializeHandler($args);
        $this->initializeDAOInjection();
    }

    public function execute() {
        $journalPath = array_shift($this->args);

        $this->journal = $this->getDAO('journal')->getJournalByPath($journalPath);
        if(is_null($this->journal)) {
            echo "Could not find a journal with path $journalPath".PHP_EOL;
            die();
        }

        $scope = array_shift($this->args);
        if(strlen($scope) > 0) {
            if($scope === 'issues') $data = $this->getIssues();
            if($scope === 'sections') $data = $this->getSections();
        } else {
            $data['issues'] = $this->getIssues();
            $data['sections'] = $this->getSections();
        }

        echo json_encode($data);
    }

    protected function getIssues() {
        $issuesResultSet = $this->getDAO('issue')->getIssues($this->journal->getId());
        $issues = [];

        foreach($issuesResultSet->toArray() as $issue) {
            $issues[] = DataObjectUtility::dataObjectToArray($issue);
        }
        return $issues;
    }

    protected function getSections() {
        $sectionsResultSet = $this->getDAO('section')->getJournalSections($this->journal->getId());
        $sections = [];
        foreach($sectionsResultSet->toArray() as $section) {
            $sections[] = DataObjectUtility::dataObjectToArray($section);
        }

        return $sections;
    }
}