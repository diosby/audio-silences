# Install

Install PHP 7.2 or more higher version with libxml extention to use the package.

# Basic

```bash
php cli.php --source <source-file> -t <transition> [--dtransition <milliseconds=250>] -m <min-silence> [-d <duration>] [-o <output-file>] [--debug]
```

The `transition`, `min-silence` and `duration` use milliseconds. Also the `dtransition` uses them.

The `source-file` and `output-file` are paths to files.

*Examples*

```bash
# Show JSON and basic info
php cli.php --source silence-files/silence4.xml -t 5000
# Save JSON and show basic info, use the min silence of a chapter part
php cli.php --source silence-files/silence4.xml -t 5000 -m 2000 -o ./json
# Save JSON, show basic info, use the deviation of the transition, use the max duration of a segment
php cli.php --source silence-files/silence4.xml -t 5000 -dtransition 250 -m 2000 -d 180000  --output ./json
# Show JSON, show debug info
php cli.php --source ./silence-files/silence4.xml -t 5000 -m 2000 -d 180000 --debug
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

## Min-silence

`-m <milliseconds>`
`--min-silence <milliseconds>`

A silence duration which can be used to split a long chapter (always shorter than the silence duration used to split chapters).

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

## Debug

`--debug`

It is used to show debugging info of analyzing and segmentation.
