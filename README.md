# Install

Install PHP 7.2 and the libxml extention.

# Basic

```bash
php cli.php --source <source-file> -t <transition> [--dtransition <milliseconds=250>] -p <pause> [--dpause <milliseconds>] [-d <duration>] [-o <output-file>] [-a <algorythm>] [--debug]
```

The `transition`, `pause` and `duration` use milliseconds. Also the `dtransition` and `dpause` use them.

The `source-file` and `output-file` are paths to files.

The `algorythm` is the other parser algorythm of silences.

*Examples*

```bash
# Show JSON and basic info
php cli.php --source silence-files/silence4.xml -t 5000 -p 2000
# Save JSON and show basic info
php cli.php --source silence-files/silence4.xml -t 5000 -p 2000 -o ./json
# Save JSON, show basic info, use the other algorythm
php cli.php --source silence-files/silence4.xml -t 5000 -p 2000 -d 180000 -a mt --output ./json
# Show JSON, show debug info, use the other algorythm
php cli.php --source ./silence-files/silence4.xml -t 5000 -p 2000 -d 180000 -a mt --debug
```

## Source

`-s <path>`
`--source <path>`

The path to an XML file with silence intervals.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<silences>
  <silence from="PT3M9S" until="PT3M11S" />
  <silence from="PT15M22S" until="PT15M25S" />
  <silence from="PT28M23S" until="PT28M26.4S" />
</silences>
```

## Transition and its deviation

`-t <milliseconds> [--dtransition <milliseconds=250>]`
`--transition <milliseconds> [--dtransition <milliseconds=250>]`

The silence duration which reliably indicates a chapter transition.
The `dtransition`is a deviation of the transition.

## Pause and its deviation

`-p <milliseconds> [--dpause <milliseconds>]`
`--pause <milliseconds> [--dpause <milliseconds>]`

A silence duration which can be used to split a long chapter (always shorter than the silence duration used to split chapters).
The `dpause`is a deviation of the pause.

It works only with the `default` algorythm.

## Duration

`-d <milliseconds>`
`--duration <milliseconds>`

The maximum duration of a segment, after which the chapter will be broken up into multiple segments.

## Output file

`-o <path>`
`--output <path>`

The file path to save a result.

```json
{
  "segments": [
    {
       "title": "Chapter 1, part 1",
       "offset": "PT0S"
    },
    {
       "title": "Chapter 1, part 2",
       "offset": "PT31M12S"
    },
    {
       "title": "Chapter 2",
       "offset": "PT47M20.5S"
    },
    {
       "title": "Chapter 3, part 1",
       "offset": "PT1H7M5S"
    },
    {
       "title": "Chapter 3, part 2",
       "offset": "PT1H30M12S"
    },
    {
       "title": "Chapter 3, part 3",
       "offset": "PT2H1M10S"
    }
  ]
}
```

## Algorythm

`-a mt`
`--algorithm mt`

An algorythm of the analyzer.

The second algorythm (`-a mt`) uses a minimal transition to detect transitions of chapters.

## Debug

`--debug`

It is used to show debugging info of analyzing and segmentation.
