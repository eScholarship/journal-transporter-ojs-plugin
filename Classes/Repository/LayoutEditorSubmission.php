<?php namespace JournalTransporterPlugin\Repository;

class LayoutEditorSubmission {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'layoutEditorSubmission';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getSubmission($article->getId());
    }
}
