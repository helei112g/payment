<?php

return [
    // 目标版本
    'target_php_version' => '7.0',

    // 需要检查的目录
    'directory_list' => [
        'src',
    ],

    // 忽略分析的目录
    'exclude_analysis_directory_list' => [
        'vendor/',
        'examples/',
    ],
];
