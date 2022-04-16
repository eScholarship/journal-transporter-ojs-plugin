<?php namespace CdlExportPlugin\Api;

class Files extends ApiRoute {
    protected $fileRepository;
    protected $articleRepository;

    public function execute($parameters, $arguments)
    {
        list($fileId, $revision) = explode('-', $parameters['file']);

        // This `article` parameter only shows up when the request is forwarded from the article/
        if(!is_null($parameters['article'])) {
            $article = $this->articleRepository->fetchById($parameters['article']);
            $file = $this->fileRepository->fetchByIdAndArticle((int) $fileId, $article, (int) $revision ?: 0);
        } else {
            $file = $this->fileRepository->fetchById((int) $fileId, (int) $revision ?: 0);
        }

        $fp = fopen($file->getFilePath(), 'rb');
        header("Content-Type: ". $file->getFileType());
        header("Content-Length: " . filesize($file->getFilePath()));
        header("Content-Disposition: attachment; filename=".$this->getValidFilename($file->getOriginalFilename()));

        fpassthru($fp);
        exit;
    }

    /**
     * Lots of examples of filenames ending in `?origin=ojsimport`. Fix generally, but quickly
     * @param $filename
     * @return mixed
     */
    protected function getValidFilename($filename)
    {
        return parse_url($filename)['path'];
    }

}