<?php


declare( strict_types = 1 );

return [

    'minimum_target_php_version' => '8.2',
    'target_php_version' => '8.2',

    'directory_list' => [
        'src/',
        'tests/',
        'vendor/',
    ],

    'exclude_analysis_directory_list' => [
        'vendor/',
    ],

    'exclude_file_list' => [
        'tests/LonelyTest.php',
    ],

    'processes' => 1,

    'analyze_signature_compatibility' => true,
    'simplify_ast' => true,
    'generic_types_enabled' => true,
    'scalar_implicit_cast' => false,

];

