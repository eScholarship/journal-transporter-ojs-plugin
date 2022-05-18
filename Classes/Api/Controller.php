<?php namespace JournalTransporterPlugin\Api;

use JournalTransporterPlugin\Utility\RegexUtility;
use JournalTransporterPlugin\Api\Journals\Sections;
use JournalTransporterPlugin\Api\Journals\Issues;
use JournalTransporterPlugin\Api\Journals\Roles;
use JournalTransporterPlugin\Api\Journals\Articles\Files\Revisions;
use JournalTransporterPlugin\Api\Journals\Articles;
use JournalTransporterPlugin\Api\Journals\Articles\Digest\Emails;
use JournalTransporterPlugin\Api\Journals\Articles\Digest\Log;
use JournalTransporterPlugin\Api\Journals\Articles\Reviews;
use JournalTransporterPlugin\Api\Journals\Articles\Reviews\Review;
use JournalTransporterPlugin\Api\Journals\Articles\Reviews\Review\FormResponses;
use JournalTransporterPlugin\Api\Journals\Articles\Files as ArticleFiles;
use JournalTransporterPlugin\Api\Journals\Articles\Authors as ArticleAuthors;
use JournalTransporterPlugin\Api\Journals\Articles\Synthetics\History;
use JournalTransporterPlugin\Api\Journals\ReviewForms;
use JournalTransporterPlugin\Api\Journals\ReviewFormElements;

class Controller {

    private $args = [];

    private $routes = [
        // Don't use a ~ character in these route regexes, unless you escape them, kew? We're using them
        // as the delimiter. Note we're using named parameters too.
        '^/journals(/(?P<journal>[a-zA-Z0-9_]+))?$' => Journals::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/sections(/(?P<section>\d+))?$' => Sections::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/issues(/(?P<issue>\d+))?$' => Issues::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/roles' => Roles::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles(/(?P<article>\d+))?$' => Articles::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/digest/emails(\.(?P<format>[a-z]+))?$' => Emails::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/files/(?P<file>\d+)/revisions$' => Revisions::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/files(/(?P<file>(\d+|\d+-\d+)))?$' => ArticleFiles::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/authors$' => ArticleAuthors::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/digest/log$' => Log::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/reviews$' => Reviews::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/review_forms(/(?P<review_form>\d+))?$' => ReviewForms::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/review_forms/(?P<review_form>\d+)/elements(/(?P<review_form_element>\d+))?$' => ReviewFormElements::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/reviews/rounds/(?P<round>\d+)(/review/(?P<review>\d+))?$' => Review::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/reviews/rounds/(?P<round>\d+)/review/(?P<review>\d+)/form_responses$' => FormResponses::class,
        '^/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/synthetics/history' => History::class,
        '^/users(/(?P<user>\d+))$' => Users::class,
        '^/files/(?P<file>[\d-]+)$' => Files::class
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
            $out = ['Exception' =>
                [
                    "Provided route '".$this->args[0]."' did not match defined routes",
                    'routeRegexes' => array_keys($this->routes)
                ]
            ];
        }

        if(!$skipRouteLookup) {
            list($head, $tail) = explode('?', $this->args[0], 2);
            foreach ($this->routes as $route => $class) {
                $matches = [];
                if (preg_match('~' . $route . '~', $head, $matches)) {
                    $out = $this->callRouteHandler($route, $class, $matches, $this->parseArguments($tail));
                    break;
                }
            }
        }

        return $out;
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