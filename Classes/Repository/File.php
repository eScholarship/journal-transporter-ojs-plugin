<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class File
{
    /**
     * @param $id file id
     * @return mixed
     */
    public function fetchById($id, $revision = null)
    {
        return DAOFactory::get()->getDAO('articleFile')->getArticleFile($id, $revision);
    }

    /**
     * @param $id file id
     * @return mixed
     */
    public function fetchByIdAndArticle($id, $article, $revision = null)
    {
        return DAOFactory::get()->getDAO('articleFile')->getArticleFile($id, $revision, $article->getId());
    }

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('articleFile')->getArticleFilesByArticle($article->getId());
    }

    /**
     * @param $file
     * @return mixed
     */
    public function fetchRevisionsByFile($file)
    {
        return DAOFactory::get()->getDAO('articleFile')->getArticleFileRevisions($file->getFileId());
    }

    /**
     * @param $file
     * @param $upTo
     * @return mixed
     */
    public function fetchFileRevisionsUpTo($file, $upTo)
    {
        return DAOFactory::get()->getDAO('articleFile')->
            getArticleFileRevisionsInRange($file->getFileId(), 1, $upTo);
    }
}
