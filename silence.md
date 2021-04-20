# Assignment: divide an audio stream by silence

## Background

Business-to-business digital content distribution is a mish-mash of various standards and
practices which haven't always been subjects to the best planning. At Beat, we tame this
complexity. But for the purposes of this task, we can afford ourselves the luxury of making
a few simplifications. As such, the following is hypothetical and not an accurate or
complete description of how things work.

An audio book is delivered to us as one long audio file. We would like to present our audio
books as series of chapters, where the user can select any chapter to start listening to it.
But the content owner does not provide us with a list of chapters, and no information about
where each chapter begins or ends. Fortunately for us, it turns out that the narrator always takes a
rather long break between chapters, typically around three seconds. Our strategy is therefore
to distribute a descriptor along with the audio streams so that clients can render the chapter
list and know what the offset of each chapter is. This way, when the user selects a chapter,
the player can seek into the stream to the correct position.

We then realise that some books have very long chapters, and we would like to provide
finer granularity in these cases. By allowing the user to skip between meaningful parts
inside each chapter, we may better approach the experience of flipping through the pages of
a paper book. Here we can divide by shorter narration breaks, such as one second.

So we want to divide books into a sequence of _segments_, where some segments are complete
short chapters, and some are parts of long chapters.

We have at our disposal a tool which detects silence intervals longer than 500 milliseconds
in audio files. After analysing a file, it produces XML like this:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<silences>
  <silence from="PT3M9S" until="PT3M11S" />
  <silence from="PT15M22S" until="PT15M25S" />
  <silence from="PT28M23S" until="PT28M26.4S" />
</silences>
```

The time offsets are ISO 8601 duration strings as described on
https://en.wikipedia.org/wiki/ISO_8601#Durations and as supported by `java.time.Duration`
and similar libraries for other environments.

In the example, there is silence which starts at 3 minutes and 9 seconds into the file, and
lasts until 3 minutes and 11 seconds – in other words, two seconds of silence. The silence
intervals are always given in chronological order.

## Assignment

Please write a command-line program which generates the segment descriptor we
need. The program should accept these command-line parameters:

* The path to an XML file with silence intervals
* The silence duration which reliably indicates a chapter transition
* The maximum duration of a segment, after which the chapter will be broken up into multiple segments
* A silence duration which can be used to split a long chapter (always shorter than the silence duration used to split chapters)

The program should output a list of segments in chronological order, following this JSON
structure:

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

The `title` field is optional, so only include this feature if time permits. We don't know
the actual chapter names, so we suggest naming them "Chapter 1", "Chapter 2" and so on.
If a chapter has been split up, we may title the segments like "Chapter 1, part 1".

Note that the initial segment starting at zero seconds must always be included.

You can choose to print the result to standard output or write it to a file.

You can assume the input XML is always valid and that its values make sense (no overlaps in
periods, no cases where "from" is higher than "until" etc), so you don't have to do a lot
of error handling. You can choose which programming language to use, and you should also
feel free to make any improvements or adjustments to the approach we have described, to
better support the end goal.
