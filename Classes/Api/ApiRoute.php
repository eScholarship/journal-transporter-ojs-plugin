<?php namespace CdlExportPlugin\Api;

class ApiRoute
{
    use RepositoryInjectionTrait;

    /**
     * Pass this URL argument to endpoints that support it for a dump of all data on the requested item (rather than
     * a curated mapping of data)
     */
    const DEBUG_ARGUMENT = 'debug';

    public function __construct()
    {
        $this->injectRepositories();
    }
}