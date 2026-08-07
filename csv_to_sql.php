<?php

declare(strict_types=1);

use Sanweb\Taskforce\components\CsvToSqlConverter\CsvToSqlConverter;

require_once __DIR__ . '/vendor/autoload.php';

$conversions = [
    [
        'csv' => __DIR__ . '/data/categories.csv',
        'sql' => __DIR__ . '/sql/categories.sql',
        'table' => 'category',
        'fields' => [
            'name' => 'name',
            'icon' => 'slug',
        ],
    ],
    [
        'csv' => __DIR__ . '/data/cities.csv',
        'sql' => __DIR__ . '/sql/cities.sql',
        'table' => 'city',
        'fields' => [
            'name' => 'name',
            'lat' => 'lat',
            'long' => 'lng',
        ],
    ],
];

foreach ($conversions as $conversion) {
    $converter = new CsvToSqlConverter($conversion['csv']);

    $converter->convert(
        outputFile: $conversion['sql'],
        table: $conversion['table'],
        fields: $conversion['fields'],
    );

    echo "Created: {$conversion['sql']}" . PHP_EOL;
}
