<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class File {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'articleFile';

    /**
     * @param $id file id
     * @return mixed
     */
    public function fetchById($id, $revision = null)
    {
        return $this->getArticleFile($id, $revision);
    }

    /**
     * @param $id file id
     * @return mixed
     */
    public function fetchByIdAndArticle($id, $article, $revision = null)
    {
        return $this->getArticleFile($id, $revision, $article->getId());
    }

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getArticleFilesByArticle($article->getId());
    }

    /**
     * @param $file
     * @return mixed
     */
    public function fetchRevisionsByFile($file, $round = null)
    {
        return $this->getArticleFileRevisions($file->getFileId(), $round);
    }

    /**
     * @param $file
     * @param $upTo
     * @return mixed
     */
    public function fetchFileRevisionsUpTo($file, $upTo)
    {
        return $this->getArticleFileRevisionsInRange($file->getFileId(), 1, $upTo);
    }
}
