<?php namespace CdlExportPlugin\Command;

use CdlExportPlugin\Utility\Traits\DAOCache;

class Api {
    use DAOCache;

    private $args = [];

    private $routes = [
        // Don't use a ~ character in these regexes, unless you escape them, kew? We're using them
        // as the wrapping token.
        '^/journals$' => \CdlExportPlugin\Api\Journals::class,
        '^/journals/(\d+)$' => \CdlExportPlugin\Api\Journals::class,
        '^/journals/(\d+)/sections$' => \CdlExportPlugin\Api\Journals\Sections::class,
        '^/journals/(\d+)/issues$' => \CdlExportPlugin\Api\Journals\Issues::class,
        '^/journals/(\d+)/articles$' => \CdlExportPlugin\Api\Journals\Articles::class,
    ];

    public function __construct($args) {
        $this->args = $args;
    }

    function execute() {
        foreach($this->routes as $route => $class) {
            $matches = [];
            if(preg_match('~'.$route.'~', $this->args[0], $matches)) {
                $apiClassInstance = new $class($matches);
                echo json_encode($apiClassInstance->execute());
                die();
            }
        }

        echo json_encode(['404' => ['A 404 on the command line?!']]);
    }
}