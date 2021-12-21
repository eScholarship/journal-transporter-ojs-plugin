<?php namespace CdlExportPlugin\Command\Journals;

use CdlExportPlugin\Command\Traits\CommandHandler;
use CdlExportPlugin\Utility\DataObjectUtility;
use CdlExportPlugin\Utility\Traits\DAOCache;

class Journal {
    use CommandHandler;
    use DAOCache;

    private $DAOs = ['journal', 'issue', 'section', 'article', 'authorSubmission', 'reviewAssignment'];

    private $journal;

    public function __construct($args) {
        $this->initializeHandler($args);
    }

    public function execute() {
        $journalPath = array_shift($this->args);

        $this->journal = $this->getDAO('journal')->getJournalByPath($journalPath);
        if(is_null($this->journal)) {
            throw new \Exception("Could not find a journal with path $journalPath");
        }

        $scope = array_shift($this->args);
        if(strlen($scope) > 0) {
            if($scope === 'issues') $data = $this->getIssues();
            if($scope === 'sections') $data = $this->getSections();
            if($scope === 'articles') $data = $this->getArticles($this->args);
        } else {
            $data = DataObjectUtility::dataObjectToArray($this->journal);
        }

        echo json_encode($data);
    }

    protected function getIssues() {
        return DataObjectUtility::resultSetToArray(
            $this->getDAO('issue')->getIssues($this->journal->getId())
        );
    }

    protected function getSections() {
        return DataObjectUtility::resultSetToArray(
            $this->getDAO('section')->getJournalSections($this->journal->getId())
        );
    }

    protected function getArticles($args = []) {
        $articleId = array_shift($args);

        $articleData = !is_null($articleId) ?
            [DataObjectUtility::dataObjectToArray($this->getDAO('article')->getArticle($articleId, $this->journal->getId()))] :
            DataObjectUtility::resultSetToArray(
                $this->getDAO('article')->getArticlesByJournalId($this->journal->getId())
            );

        // Only show all this stuff if we're display an article singly
        if(!is_null($articleId)) {
            foreach($articleData as &$article) {
                $dataMergeConfig = [
                    ['authorSubmission', 'getAuthorSubmission', 'DAO'],
                    ['editAssignment', 'getEditAssignmentsByArticleId', 'resultSet'],
                    ['editorSubmission', 'getEditorSubmission', 'DAO'],
                    ['sectionEditorSubmission', 'getSectionEditorSubmission', 'DAO'],
                    ['reviewAssignment', 'getReviewAssignmentsByArticleId', 'DAO'],
                    ['reviewerSubmission', 'getEditorDecisions', 'none'],
                    ['copyeditorSubmission', 'getCopyeditorSubmission', 'DAO'],
                    ['layoutEditorSubmission', 'getSubmission', 'DAO'],
                    ['proofreaderSubmission', 'getSubmission', 'DAO'],
                    ['articleComment', 'getArticleComments', 'DAO'],
                    ['articleFile', 'getArticleFilesByArticle', 'DAO'],
                    ['articleGalley', 'getGalleysByArticle', 'DAO'],
                    ['suppFile', 'getSuppFilesByArticle', 'DAO'],
                    ['articleEmailLog', 'getArticleLogEntries', 'resultSet'],
                    ['articleEventLog', 'getArticleLogEntries', 'resultSet']
                ];

                foreach($dataMergeConfig as $mergeConfig) {
                    $methodName = $mergeConfig[1];
                    $daoResult = $this->getDAO($mergeConfig[0])->$methodName($article->id);
                    if($mergeConfig[2] === 'DAO') {
                        $data = DataObjectUtility::dataObjectToArray($daoResult);
                    } elseif($mergeConfig[2] === 'resultSet') {
                        $data = DataObjectUtility::resultSetToArray($daoResult);
                    } else {
                        $data = $daoResult;
                    }
                    $article = DataObjectUtility::mergeWithoutRedundancy($article, $data, '__'.$mergeConfig[0]);
                }
            }
        }

        return $articleData;
    }
}