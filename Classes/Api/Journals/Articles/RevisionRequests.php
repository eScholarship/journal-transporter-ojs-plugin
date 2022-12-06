<?php namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Exception\CannotFetchDataObjectException;
use JournalTransporterPlugin\Repository\File;
use JournalTransporterPlugin\Utility\Date;
use JournalTransporterPlugin\Utility\Enums\Role;

class RevisionRequests extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $editorSubmissionRepository;
    protected $articleCommentRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);

        return @$parameters['revision_request'] ?
            $this->getRevisionRequest($parameters['revision_request'], $article, $arguments[ApiRoute::DEBUG_ARGUMENT]) :
            $this->getRevisionRequests($article);
    }

    /**
     * @param $article
     * @return array
     */
    protected function getRevisionRequests($article)
    {
        $decisions = $this->editorSubmissionRepository->fetchEditorDecisionsByArticle($article);

        return array_map(function ($decision) {
            return NestedMapper::map($this->editDecisionArrayToObject($decision), 'sourceRecordKey');
        }, $decisions);
    }

    /**
     * In order to grab the comments associated with an editor decision, we need to know the datetime of any subsequent
     * editor decisions. Hence, we fetch them all, then fetch comments that occurred within this window
     * @param $revisionRequest
     * @param $article
     * @param $debug
     * @throws
     */
    protected function getRevisionRequest($revisionRequest, $article, $debug)
    {
        $decisions = $this->editorSubmissionRepository->fetchEditorDecisionsByArticle($article);

        /**
         * Make sure the decision exists
         */
        $foundEditDecision = null;
        foreach($decisions as $decision) {
            if($decision['editDecisionId'] == $revisionRequest) {
                $foundEditDecision = $decision;
                break;
            }
        }
        if(is_null($foundEditDecision)) throw new CannotFetchDataObjectException("Edit decision $revisionRequest does not exist");

        list($commentsStart, $commentsEnd) = $this->determineCommentWindow($decisions, $revisionRequest);

        /**
         * This is a real oddball as far as rendering a response is concerned. Given the opportunity I'd circle
         * back on this.
         */
        $decision = NestedMapper::map($this->editDecisionArrayToObject($foundEditDecision));

        $decision['dateResponse'] = $this->getResponseDate($article, $decision);
        $decision['comment'] = $this->formatComments($this->fetchEditorCommentsWithinRange($article, $commentsStart, $commentsEnd));

        return $decision;
    }

    /**
     * @param $article
     * @param $decision
     * @return string|null
     * @throws \Exception
     */
    protected function getResponseDate($article, $decision)
    {
        $files = array_filter((new File)->fetchByArticle($article),
            function($f) use($decision) {
                if($f->getRound() != $decision['round']) return false;
                if(Date::strToDatetime($f->getDateUploaded()) <= Date::strToDatetime($decision['date'])) return false;
                return true;
            }
        );

        if(count($files) == 0) return null;

        usort($files, function($a, $b) {
            return $a->getDateUploaded() > $b->getDateUploaded();
        });

        return Date::formatDateString($files[0]->getDateUploaded());
    }

    /**
     * Iterate the decisions, and find the start and end datetimes for the comments we want
     * @param $decisions
     * @param $requestedDecision
     * @return null[]
     */
    protected function determineCommentWindow($decisions, $requestedDecision)
    {
        $commentsStart = null;
        $commentsEnd = null;
        foreach($decisions as $decision) {
            if(is_null($commentsEnd) && !is_null($commentsStart)) {
                $commentsEnd = new \DateTime($decision['dateDecided'], new \DateTimeZone('America/Los_Angeles'));
            }

            if(is_null($commentsStart)) {
                if($decision['editDecisionId'] == $requestedDecision) {
                    $commentsStart = new \DateTime($decision['dateDecided'], new \DateTimeZone('America/Los_Angeles'));
                }
            }
            if(!is_null($commentsStart) && !is_null($commentsEnd)) break;
        }
        return [$commentsStart, $commentsEnd];
    }

    /**
     * @param $article
     * @param $commentsStart
     * @param $commentsEnd
     * @return mixed
     */
    protected function fetchEditorCommentsWithinRange($article, $commentsStart, $commentsEnd)
    {
        $allComments = $this->articleCommentRepository->fetchEditorCommentsByArticle($article);
        $comments = array_filter($allComments,
            function($comment) use($commentsStart, $commentsEnd) {
                return (is_null($commentsStart) || Date::strToDatetime($comment->getDatePosted()) >= $commentsStart) &&
                    (is_null($commentsEnd) || Date::strToDatetime($comment->getDatePosted()) < $commentsEnd);
            }
        );
        return $comments;
    }


    /**
     * @param $comments
     * @return mixed
     */
    protected function formatComments($comments)
    {
//        print_R($comments); die();
        $digest = [];

        foreach($comments as $comment) {
            $digest[] = "
Date: ".Date::formatDateString($comment->getDatePosted())."
Role: ".ucfirst(Role::getRoleName($comment->getRoleId()))."
Subject: {$comment->getCommentTitle()}

{$comment->getComments()}
";
        }
        $separator = "\n\n".str_repeat('=', 80)."\n\n";

        $string = wordwrap(implode($separator, $digest), 80);

        return $string;
    }


    /**
     * Make a mappable stdClass from the array we get from the DAO
     * @param $decision
     * @return object
     */
    protected function editDecisionArrayToObject($decision)
    {
        $decisionObject = (object) $decision;
        $decisionObject->__mapperClass = 'EditorDecision';
        return $decisionObject;
    }

}