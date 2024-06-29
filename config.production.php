<?php

return [
    'baseUrl' => 'https://apepin.dev',
    'production' => true,

    // Collections
    'collections' => [
        'posts' => [
            'filter' => function ($item) {
                return $item->published;
            },
        ],
        'categories' => [
            //
        ],
    ],
];
