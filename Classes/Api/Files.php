<?php namespace CdlExportPlugin\Api;

class Files extends ApiRoute {
    protected $fileRepository;

    public function execute($parameters, $arguments)
    {
        $file = $this->fileRepository->fetchById($parameters['file']);

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