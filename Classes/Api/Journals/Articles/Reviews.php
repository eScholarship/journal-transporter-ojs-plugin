<?php namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObjectUtility;

class Reviews extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $editAssignmentRepository;
    protected $sectionEditorSubmissionRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $editAssignments = $this->editAssignmentRepository->fetchByArticle($article)->toArray();
        $numberOfRounds = $this->sectionEditorSubmissionRepository->fetchNumberOfRoundsByArticle($article);

        for($i = 1; $i <= $numberOfRounds; $i++) {
            $editorDecisions[$i] = $this->sectionEditorSubmissionRepository->fetchEditorDecisionsByArticle($article, $i);
        }
        $flattenedEditorDecisions = array_merge([], ...$editorDecisions);

        return [
            'editors' => array_map(function($item) {
                return NestedMapper::map($item);
            }, $editAssignments),
            'decisions' => array_map(function($item) {
                return NestedMapper::map((object) ($item + ['__mapperClass' => 'EditorDecision']));
            }, $flattenedEditorDecisions),
            'numberOfRounds' => $numberOfRounds
        ];

    }
}