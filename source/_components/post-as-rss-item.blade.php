@php
    $filepath = str_replace('/', '.', $entry->getRelativePath()) . ".{$entry->getFilename()}"
@endphp

<entry>
    <id>{{ $entry->getUrl() }}</id>
    <link type="text/html" rel="alternate" href="{{ $entry->getUrl() }}" />
    <title>{{ $entry->title }}</title>
    <published>{{ date(DATE_ATOM, $entry->date) }}</published>
    <updated>{{ date(DATE_ATOM, $entry->date) }}</updated>
    <author>
        <name>{{ $entry->author }}</name>
    </author>
    <summary type="html">{{ $entry->getExcerpt() }}...</summary>
    <content type="html"><![CDATA[
        @includeFirst(["_posts.$filepath", "_posts._tmp.$filepath"])
    ]]></content>
</entry>
