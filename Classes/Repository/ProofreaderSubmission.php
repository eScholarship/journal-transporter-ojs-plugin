<?php namespace JournalTransporterPlugin\Repository;

class ProofreaderSubmission {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'proofreaderSubmission';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getSubmission($article->getId());
    }
}
