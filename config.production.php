<?php

return [
    'baseUrl' => 'https://apepindev.github.io',
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
