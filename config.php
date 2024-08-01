<?php

use Illuminate\Support\Str;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;

return [
    'baseUrl' => '',
    'production' => false,
    'siteName' => 'apepin.dev',
    'siteDescription' => 'Generate an elegant blog with Jigsaw',
    'siteAuthor' => 'Andrew',

    // collections
    'merge_collection_configs' => true,
    'collections' => [
        'posts' => [
            'author' => 'Andrew', // Default author, if not provided in a post
            'sort' => '-date',
            'path' => 'posts/{-title}',
            'filter' => function ($item) {
//                return $item->getRelativePath() !== 'jigsaw';
                return $item;
            },
        ],
        'categories' => [
            'path' => '/posts/categories/{filename}',
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

    'commonmark' => [
        'config' => [
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break'      => "\n",
            ],
            'commonmark' => [
                'enable_em' => true,
                'enable_strong' => true,
                'use_asterisk' => true,
                'use_underscore' => true,
                'unordered_list_markers' => ['-', '*', '+'],
            ],
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'max_nesting_level' => PHP_INT_MAX,
            'slug_normalizer' => [
                'max_length' => 255,
            ],
            'heading_permalink' => [
                'html_class' => 'heading-permalink',
                'insert' => 'after',
                'title' => 'Permalink',
                'symbol' => '#',
            ],
            'smartpunct' => [
                'double_quote_opener' => '“',
                'double_quote_closer' => '”',
                'single_quote_opener' => '‘',
                'single_quote_closer' => '’',
            ],
            'table' => [
                'wrap' => [
                    'enabled' => false,
                    'tag' => 'div',
                    'attributes' => [],
                ],
                'alignment_attributes' => [
                    'left'   => ['align' => 'left'],
                    'center' => ['align' => 'center'],
                    'right'  => ['align' => 'right'],
                ],
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
