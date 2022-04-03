<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class ReviewAssignment extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'reviewId'],
        ['property' => 'reviewerId'],
        ['property' => 'fullName', 'source' => 'reviewerFullName'],
        ['property' => 'dateRated', 'filters' => ['datetime']],
        ['property' => 'lastModified', 'filters' => ['datetime']],
        ['property' => 'dateAssigned', 'filters' => ['datetime']],
        ['property' => 'dateNotified', 'filters' => ['datetime']],
        ['property' => 'dateConfirmed', 'filters' => ['datetime']],
        ['property' => 'dateCompleted', 'filters' => ['datetime']],
        ['property' => 'dateAcknowledged', 'filters' => ['datetime']],
        ['property' => 'dateReminded', 'filters' => ['datetime']],
        ['property' => 'dateDue', 'filters' => ['datetime']],
        ['property' => 'dateResponseDue', 'filters' => ['datetime']],
        ['property' => 'declined', 'filters' => ['boolean']],
        ['property' => 'replaced', 'filters' => ['boolean']],
        ['property' => 'cancelled', 'filters' => ['boolean']],
        ['property' => 'reviewFile'],
        ['property' => 'reviewerFile'],
    ];
}