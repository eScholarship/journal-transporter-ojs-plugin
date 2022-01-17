<?php namespace CdlExportPlugin\Command;

use CdlExportPlugin\Utility\RegexUtility;
use CdlExportPlugin\Utility\Traits\DAOCache;

class Api {
    use DAOCache;

    private $args = [];

    private $routes = [
        // Don't use a ~ character in these route regexes, unless you escape them, kew? We're using them
        // as the delimiter. Note we're using named parameters too.
        '^/journals(/(?P<journal>\d+))?$' => \CdlExportPlugin\Api\Journals::class,
        '^/journals/(?P<journal>\d+)/sections$' => \CdlExportPlugin\Api\Journals\Sections::class,
        '^/journals/(?P<journal>\d+)/issues$' => \CdlExportPlugin\Api\Journals\Issues::class,
        '^/journals/(?P<journal>\d+)/articles(/(?P<article>\d+))?$' => \CdlExportPlugin\Api\Journals\Articles::class,
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
        $skipRouteLookup = false;
        if(is_null($this->args[0])) {
            $out = ['Exception' => 'No route provided', 'routeRegexes' => array_keys($this->routes)];
            $skipRouteLookup = true;
        } else {
            $out = ['Exception' => ["Provided route '".$this->args[0]."' did not match defined routes"]]; // Fallback response
        }

        if(!$skipRouteLookup) {
            list($head, $tail) = explode('?', $this->args[0], 2);
            foreach ($this->routes as $route => $class) {
                $matches = [];
                if (preg_match('~' . $route . '~', $head, $matches)) {
                    $out = $this->callApiMethod($route, $class, $matches, $this->parseArguments($tail));
                    break;
                }
            }
        }
        echo json_encode($out).PHP_EOL;
    }

    /**
     * Turns ?k1=v1&k2=v2 into key value pairs. Not used yet.
     * @param $argumentsString
     * @return array
     */
    private function parseArguments($argumentsString) {
        $pairs = explode('&', $argumentsString);
        $arguments = [];
        foreach($pairs as $pair) {
            list($key, $value) = explode('=', $pair);
            $arguments[$key] = is_null($value) ?: $value;
        }
        return $arguments;
    }

    /**
     * Call API method, catch exceptions
     * @param $route
     * @param $class
     * @param $routeParameters
     * @return array|mixed
     */
    private function callApiMethod($route, $class, $routeParameters, $arguments = []) {
        $parameters = $this->zipArgs($routeParameters, RegexUtility::getRegexNamedMatches($route));

        try {
            $out = (new $class($routeParameters))->execute($parameters, $arguments);
        } catch(\Exception $e) {
            // TODO: this could be make more robust exception handling
            $out = ['Exception' => $e->getMessage()];
        }
        return $out;
    }

    /**
     * Given an array of matches and an array of allowed params, return just the params from the matches that are
     * allowed
     * @param $rawArgs
     * @param $allowedParameters
     * @return array
     */
    private function zipArgs($rawArgs, $allowedParameters) {
        $out = [];
        foreach($allowedParameters as $allowedParameter) {
            $out[$allowedParameter] = @$rawArgs[$allowedParameter];
        }
        return $out;
    }
}