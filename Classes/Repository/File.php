<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\DAOFactory;

class File
{
    /**
     * @param $id file id
     * @return mixed
     */
    public function fetchById($id)
    {
        return DAOFactory::get()->getDAO('articleFile')->getArticleFile($id);
    }

    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('articleFile')->getArticleFilesByArticle($article->getId());
    }

    public function fetchRevisionsByFile($file)
    {
        return DAOFactory::get()->getDAO('articleFile')->getArticleFileRevisions($file->getFileId());
    }
}
