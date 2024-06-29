<?php

use Illuminate\Support\Str;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;

return [
    'baseUrl' => '',
    'production' => false,
    'siteName' => 'apepin.dev',
    'siteDescription' => 'Generate an elegant blog with Jigsaw',
    'siteAuthor' => 'Andrew',

    'commonmark' => [
        'config' => [
            'heading_permalink' => [
                'html_class' => 'heading-permalink',
                'insert' => 'after',
                'title' => "Permalink",
                'symbol' => HeadingPermalinkRenderer::DEFAULT_SYMBOL,
            ],
        ],
        'extensions' => [
            new AttributesExtension,
            new SmartPunctExtension,
            new StrikethroughExtension,
            new TableExtension,
            new HeadingPermalinkExtension,
        ],
    ],

    // collections
    'merge_collection_configs' => true,
    'collections' => [
        'posts' => [
            'author' => 'Andrew', // Default author, if not provided in a post
            'sort' => '-date',
            'path' => 'blog/{filename}',
            'filter' => function ($item) {
                return $item;
            },
        ],
        'categories' => [
            'path' => '/blog/categories/{filename}',
            'posts' => function ($page, $allPosts) {
                return $allPosts->filter(function ($post) use ($page) {
                    return $post->categories ? in_array($page->getFilename(), $post->categories, true) : false;
                });
            },
        ],
    ],

    // helpers
    'getDate' => function ($page) {
        return Datetime::createFromFormat('U', $page->date);
    },
    'getExcerpt' => function ($page, $length = 255) {
        if ($page->excerpt) {
            return $page->excerpt;
        }

        $content = preg_split('/<!-- more -->/m', $page->getContent(), 2);
        $cleaned = textOnly($content[0]);

        if (count($content) > 1) {
            return $cleaned;
        }

        $truncated = substr($cleaned, 0, $length);

        if (substr_count($truncated, '<code>') > substr_count($truncated, '</code>')) {
            $truncated .= '</code>';
        }

        return strlen($cleaned) > $length
            ? preg_replace('/\s+?(\S+)?$/', '', $truncated) . '...'
            : $cleaned;
    },
    'getReadTime' => function ($page) {
        $cleaned = textOnly($page->getContent());

        $time = round(str_word_count($cleaned) / 200);

        if ($time > 0) {
            return "$time " . Str::plural('minute', $time);
        }

        return "< 1 minute";
    },
    'isActive' => function ($page, $path) {
        return Str::contains(trimPath($page->getPath()), trimPath($path));
    },
];

function textOnly(string $content): string
{
    return trim(
        strip_tags(
            preg_replace(['/<pre>[\w\W]*?<\/pre>/', '/<h\d>[\w\W]*?<\/h\d>/'], '', $content),
            '<code>'
        )
    );
}
