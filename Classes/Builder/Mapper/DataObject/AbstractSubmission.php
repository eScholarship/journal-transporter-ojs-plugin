<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class AbstractSubmission extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'editAssignments'],
        ['property' => 'galleys'],
        ['property' => 'supplementaryFiles', 'source' => 'suppFiles']
    ];
}