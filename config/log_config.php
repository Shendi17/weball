<?php

return [
    'search_log' => [
        'file' => __DIR__ . '/../logs/search.log',
        'max_size' => 5242880, // 5 MB
        'max_files' => 5 // Garder 5 fichiers de rotation maximum
    ]
];
