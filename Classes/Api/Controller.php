<?php namespace JournalTransporterPlugin\Api;

use JournalTransporterPlugin\Exception\CannotFetchDataObjectException;
use JournalTransporterPlugin\Api\Response;
use JournalTransporterPlugin\Utility\Regex;
use JournalTransporterPlugin\Exception\PluginException;

class Controller {

    private $args = [];

    private $routes = [];

    /**
     * Api constructor.
     * @param $args
     */
    public function __construct($args) {
        $this->args = $args;
        $this->routes = include('Routes.php');
    }

    /**
     *
     */
    public function execute() {
        $skipRouteLookup = false;
        $response = new Response;
        if(is_null($this->args[0])) {
            $response->setPayload(['exception' => 'No route provided', 'route_regexes' => array_keys($this->routes)]);
            $response->setResponseCode(500);
            $skipRouteLookup = true;
        } else {
            $response->setPayload([
                'exception' => "Provided route '".$this->args[0]."' did not match defined routes",
                'route_regexes' => array_keys($this->routes)
            ]);
            $response->setResponseCode(500);
        }

        if(!$skipRouteLookup) {
            list($head, $tail) = explode('?', $this->args[0], 2);
            foreach ($this->routes as $route => $class) {
                $matches = [];
                if (preg_match('~^' . $route . '$~', $head, $matches)) {
                    $response = $this->callRouteHandler($route, $class, $matches, $this->parseArguments($tail));
                    break;
                }
            }
        }

        return $response;
    }

    /**
     * Call API method, catch exceptions
     * @param $route
     * @param $class
     * @param $routeParameters
     * @param array $arguments
     * @return array
     */
    private function callRouteHandler($route, $class, $routeParameters, $arguments = []) {
        $parameters = $this->zipArgs($routeParameters, Regex::getRegexNamedMatches($route));

        // Typically the route controllers just return a payload, and we expect it be JSON. However,
        // they can also return a response object, in which case we just pass it through.
        $response = new Response;
        try {
            $payload = (new $class($routeParameters))->execute($parameters, $arguments);
            if($payload instanceof Response) {
                $response = $payload;
            } else {
                $response->setPayload($payload);
            }
        } catch(CannotFetchDataObjectException $e) {
            $response->setPayload($e->getMessage());
            $response->setResponseCode('404');
        } catch(\Exception $e) {
            $response->setPayload($e->getMessage());
            $response->setResponseCode('500');
        }

        return $response;
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

    /**
     * This is pretty naive, but also good enough for our purposes.
     * Turns ?k1=v1&k2=v2 into key value pairs.
     * @param $argumentsString
     * @return array
     */
    private function parseArguments($argumentsString) {
        $decodedArgumentsString = urldecode($argumentsString);

        $arguments = [];
        if(strlen($decodedArgumentsString) > 0) {
            $pairs = explode('&', $decodedArgumentsString);
            foreach ($pairs as $pair) {
                list($key, $value) = explode('=', $pair);
                if (substr($key, -2) == '[]') {
                    if (is_array($arguments[$key])) {
                        $arguments[$key][] = $value;
                    } else {
                        $arguments[$key] = [$value];
                    }
                } else {
                    $arguments[$key] = is_null($value) ?: $value;
                }
            }
        }
        return $arguments;
    }
}