<?php

/**
 * Takes mapping from Builder/Mapper/DataObject classes and formats it nicely.
 *
 * Run me from the command line, it would look like this:
 *
 * $ php plugins/generic/cdlExport/script/format_mapping.php
 * id
 * sectionTitle -> section
 * articleTitle -> title
 * authors
 * statusKey
 * ^D
 *
 */

$lines = [];
while($line = fgets(STDIN)) {
    $lines[] = $line;
}

$longestTheirs = 0;
$longestOurs = 0;

$parsedLines = [];
foreach ($lines as $mappingConfig) {

    $parts = explode('|', $mappingConfig);
    $fieldConfig = trim(array_shift($parts));

    $trimmed = trim($fieldConfig);

    list($left, $middle) = explode('->', $trimmed);
    $left = trim($left);
    $middle = trim($middle);
    $left = trim($left);

    if(strlen($left) > 0 && strlen($middle) == 0) {
        $middle = $left;
        $left = '';
    }

    $right = trim(implode(' | ', $parts));

    if(strlen($left) > 0) $left = $left . (strlen($middle) > 0 ? ' ->' : '   ');

    if(strlen($right)) {
        $right = '| '.$right;
    }

    $parsedLines[] = (object) ['theirs' => $left, 'ours' => trim($middle), 'filters' => $right];

    $longestTheirs = strlen($left) > $longestTheirs ? strlen($left) : $longestTheirs;
    $longestOurs = strlen($middle) > $longestOurs ? strlen($middle) : $longestOurs;
}

$out = '';
foreach($parsedLines as $parsedLine) {
    $out .= PHP_EOL . "\t\t" . sprintf("%${longestTheirs}s", $parsedLine->theirs) . ' '.
        sprintf("%-${longestOurs}s", $parsedLine->ours) . ' '.
        $parsedLine->filters;
}
echo $out;
echo PHP_EOL;

