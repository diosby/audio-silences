<?php

require __DIR__ . '/vendor/autoload.php';

use SegmentGenerator\ChapterAnalyzer;
use SegmentGenerator\ChapterGenerator;
use SegmentGenerator\ChapterSegmentator;
use SegmentGenerator\Interval;
use SegmentGenerator\Silence;

// Expected arguments of CLI.
$shortopts = '';
$longopts = [];

// A file path
$shortopts .= 's:';
$longopts[] = 'source:';

// A chapter transition
$shortopts .= 't:';
$longopts[] = 'transition:';

// A deviation of the transition
$longopts[] = 'dtransition:';

// A minimal silence between parts (segments) of a chapter
$shortopts .= 'm:';
$longopts[] = 'min-silence:';

// A duration of a segment in multiple segments
$shortopts .= 'd:';
$longopts[] = 'max-duration:';

// An output file
$shortopts .= 'o:';
$longopts[] = 'output:';

// A debug mode
$longopts[] = 'debug::';

$options = getopt($shortopts, $longopts);

// Gets arguments.
$path = $options['source'] ?? $options['s'] ?? null;
$transition = $options['transition'] ?? $options['t'] ?? null;
$deviationOfTransition = $options['dtransition'] ?? 250;
$minSilence = $options['min-silence'] ?? $options['m'] ?? null;
$maxDuration = $options['max-duration'] ?? $options['d'] ?? null;
$output = $options['output'] ?? $options['o'] ?? null;
$debug = isset($options['debug']) ? empty($options['debug']) : false;

if (empty($path)) {
    exit("The path to a file wasn't given. Set the path to a file through --source <path> or -s <path>.\n");
}

if (empty($transition)) {
    exit("The chapter transition wasn't given. Set the transition through --transition <duration> or -t <duration>. The duration should be greater than zero.\n");
} elseif (!is_numeric($transition)) {
    exit("The given transition isn't a number. The value should be an integer.\n");
}

if (!file_exists($path)) {
    exit("The $path file doesn't exist.\n");
}

$xml = simplexml_load_file($path);
/** @var Silence[] $silences */
$silences = [];

foreach ($xml as $item) {
    $silences[] = new Silence(new Interval($item['from']), new Interval($item['until']));
}

$analizer = new ChapterAnalyzer($transition, $deviationOfTransition);
$generator = new ChapterGenerator($analizer);
$generator->debugMode($debug);
$chapters = $generator->fromSilences($silences);

// Generates template titles.
$chapters->fillTitles();

$segmentator = new ChapterSegmentator($maxDuration, $minSilence);
$segmentator->debugMode($debug);
$segments = $segmentator->segment($chapters);

printf("The file: %s.\n", $path);
printf("The transition: %dms.\n", $transition);
printf("The deviation of the transition: %dms.\n", $deviationOfTransition);
printf("A number of the chapters: %d.\n", $chapters->getNumberOfChapters());
printf("A number of the parts of the chapters: %d.\n", $chapters->getNumberOfParts());
printf("A duration of the chapters without silences between chapters: %dms.\n", $chapters->getDuration());

$data = ['segments' => $segments->toArray()];

if (isset($output)) {
    file_put_contents($output, json_encode($data, JSON_PRETTY_PRINT));
} else {
    print(json_encode($data, JSON_PRETTY_PRINT)."\n");
}
