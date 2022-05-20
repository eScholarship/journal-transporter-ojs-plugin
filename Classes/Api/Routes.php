<?php

use JournalTransporterPlugin\Api\Files;
use JournalTransporterPlugin\Api\Journals;
use JournalTransporterPlugin\Api\Journals\Articles;
use JournalTransporterPlugin\Api\Journals\Articles\Authors as ArticleAuthors;
use JournalTransporterPlugin\Api\Journals\Articles\Digest\Emails;
use JournalTransporterPlugin\Api\Journals\Articles\Digest\Log;
use JournalTransporterPlugin\Api\Journals\Articles\Files as ArticleFiles;
use JournalTransporterPlugin\Api\Journals\Articles\Files\Revisions;
use JournalTransporterPlugin\Api\Journals\Articles\Rounds;
use JournalTransporterPlugin\Api\Journals\Articles\Rounds\ReviewAssignments;
use JournalTransporterPlugin\Api\Journals\Articles\Rounds\ReviewAssignments\FormResponses;
use JournalTransporterPlugin\Api\Journals\Articles\Synthetics\History;
use JournalTransporterPlugin\Api\Journals\Issues;
use JournalTransporterPlugin\Api\Journals\ReviewForms;
use JournalTransporterPlugin\Api\Journals\ReviewForms\Elements;
use JournalTransporterPlugin\Api\Journals\Roles;
use JournalTransporterPlugin\Api\Journals\Sections;
use JournalTransporterPlugin\Api\Users;

return [
    /**
     * Notes:
     *  - Don't use a ~ character in these route regexes, unless you escape them. We're using them
     *    as the delimiter.
     *  - No need to add ^ or $ either, those are added before evaluation
     *  - We're using named parameters.
     */
    '/journals(/(?P<journal>[a-zA-Z0-9_]+))?' => Journals::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/sections(/(?P<section>\d+))?' => Sections::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/issues(/(?P<issue>\d+))?' => Issues::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/roles' => Roles::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles(/(?P<article>\d+))?' => Articles::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/digest/emails(\.(?P<format>[a-z]+))?' => Emails::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/files/(?P<file>\d+)/revisions' => Revisions::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/files(/(?P<file>(\d+|\d+-\d+)))?' => ArticleFiles::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/authors' => ArticleAuthors::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/digest/log' => Log::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/rounds/(?P<round>\d+)/assignments(/(?P<review_assignment>\d+))?' => ReviewAssignments::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/rounds/(?P<round>\d+)/assignments/(?P<review_assignment>\d+)/response' => FormResponses::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/rounds(/(?P<round>\d)+)?' => Rounds::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/articles/(?P<article>\d+)/synthetics/history' => History::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/review_forms(/(?P<review_form>\d+))?' => ReviewForms::class,
    '/journals/(?P<journal>[a-zA-Z0-9_]+)/review_forms/(?P<review_form>\d+)/elements(/(?P<review_form_element>\d+))?' => Elements::class,
    '/users(/(?P<user>\d+))$' => Users::class,
    '/files/(?P<file>[\d-]+)$' => Files::class
];