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
                $article->__authorSubmission = DataObjectUtility::dataObjectToArray($this->getDAO('authorSubmission')->getAuthorSubmission($article->id));
                $article->__editAssignment = DataObjectUtility::resultSetToArray($this->getDAO('editAssignment')->getEditAssignmentsByArticleId($article->id));
                $article->__editorSubmission = DataObjectUtility::dataObjectToArray($this->getDAO('editorSubmission')->getEditorSubmission($article->id));
                $article->__sectionEditorSubmission = DataObjectUtility::dataObjectToArray($this->getDAO('sectionEditorSubmission')->getSectionEditorSubmission($article->id));
                $article->__reviewAssignment = $this->getDAO('reviewAssignment')->getReviewAssignmentsByArticleId($article->id);
                $article->__reviewerSubmission = $this->getDAO('reviewerSubmission')->getEditorDecisions($article->id);
                $article->__copyeditorSubmission = DataObjectUtility::dataObjectToArray($this->getDAO('copyeditorSubmission')->getCopyeditorSubmission($article->id));
                $article->__layoutEditorSubmission = DataObjectUtility::dataObjectToArray($this->getDAO('layoutEditorSubmission')->getSubmission($article->id));
                $article->__proofreaderSubmission = DataObjectUtility::dataObjectToArray($this->getDAO('proofreaderSubmission')->getSubmission($article->id));


                $article->__articleComments = DataObjectUtility::dataObjectToArray($this->getDao('articleComment')->getArticleComments($article->id));
                $article->__articleFile = DataObjectUtility::dataObjectToArray($this->getDao('articleFile')->getArticleFilesByArticle($article->id));
                $article->__articleGalley = DataObjectUtility::dataObjectToArray($this->getDao('articleGalley')->getGalleysByArticle($article->id));
                $article->__suppFile = DataObjectUtility::dataObjectToArray($this->getDao('suppFile')->getSuppFilesByArticle($article->id));
                $article->__articleEmailLog = DataObjectUtility::resultSetToArray($this->getDAO('articleEmailLog')->getArticleLogEntries($article->id));
                $article->__articleEventLog = DataObjectUtility::resultSetToArray($this->getDAO('articleEventLog')->getArticleLogEntries($article->id));
            }
        }

        return $articleData;
    }
}