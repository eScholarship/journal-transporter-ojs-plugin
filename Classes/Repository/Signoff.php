<?php namespace JournalTransporterPlugin\Repository;

class Signoff {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'signoff';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getByAssoc('257', $article->getId());
    }
}
