<?php namespace CdlExportPlugin\Command;

use CdlExportPlugin\Utility\Traits\DAOCache;

class Api {
    use DAOCache;

    private $args = [];

    private $routes = [
        // Don't use a ~ character in these route regexes, unless you escape them, kew? We're using them
        // as the delimiter. Note we're using named parameters too.
        '^/journals(/(?P<journal>\d+))?$' => \CdlExportPlugin\Api\Journals::class,
        '^/journals/(?P<journal>\d+)/sections(/(?P<section>\d+))?$' => \CdlExportPlugin\Api\Journals\Sections::class,
        '^/journals/(\d+)/issues$' => \CdlExportPlugin\Api\Journals\Issues::class,
        '^/journals/(\d+)/articles$' => \CdlExportPlugin\Api\Journals\Articles::class,
    ];

    /**
     * Api constructor.
     * @param $args
     */
    public function __construct($args) {
        $this->args = $args;
    }

    /**
     *
     */
    public function execute() {
        $out = ['Exception' => ["Route '".$this->args[0]."' did not match defined routes"]]; // Fallback response
        foreach($this->routes as $route => $class) {
            $matches = [];
            if(preg_match('~'.$route.'~', $this->args[0], $matches)) {
                $out = $this->callApiMethod($route, $class, $matches);
                break;
            }
        }
        echo json_encode($out).PHP_EOL;
    }

    /**
     * Call API method, catch exceptions
     * @param $route
     * @param $class
     * @param $matches
     * @return array|mixed
     */
    private function callApiMethod($route, $class, $matches) {
        $args = $this->zipArgs($matches, $this->getRegexNamedMatches($route));

        try {
            $out = (new $class($matches))->execute($args);
        } catch(\Exception $e) {
            // TODO: this could be make more robust exception handling
            $out = ['Exception' => $e->getMessage()];
        }
        return $out;
    }

    /**
     * Extract the named matches from a regex, with a regex!
     * @param $route
     * @return array|mixed
     */
    private function getRegexNamedMatches($route) {
        return preg_match_all('/\?P<([a-zA-Z0-9_]+)>/', $route, $matches) ? $matches[1] : [];
    }

    /**
     * Given an array of matches and an array of allowed params, return just the params from the matches that are
     * allowed
     * @param $rawArgs
     * @param $allowedParams
     * @return array
     */
    private function zipArgs($rawArgs, $allowedParams) {
        $out = [];
        foreach($allowedParams as $allowedParam) {
            $out[$allowedParam] = @$rawArgs[$allowedParam];
        }
        return $out;
    }
}