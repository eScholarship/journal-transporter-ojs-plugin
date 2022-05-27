<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class SupplementaryFile {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'suppFile';

    /**
     * The revision field isn't returned by the query! So, we have to extract it from the filename field.
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        $suppFiles = DAOFactory::get()->getDAO('suppFile')->getSuppFilesByArticle($article->getId());
        foreach($suppFiles as &$suppFile) {
            if(!is_null($suppFile->getRevision())) continue;
            $suppFile->_data['revision'] = self::extractRevisionFromFileName($suppFile->getFilename());
        }

        return $suppFiles;
    }

    /**
     * @param $filename
     * @return int
     */
    public static function extractRevisionFromFileName($filename)
    {
        list(,, $revision) = explode('-', $filename);
        return (int) $revision;
    }
}
