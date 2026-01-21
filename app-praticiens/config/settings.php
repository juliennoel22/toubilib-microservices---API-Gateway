<?php

$dbConfig = parse_ini_file(__DIR__ . '/praticien.ini', true);
$dbConfig = $dbConfig ? $dbConfig['database'] : [];

return [
    'settings' => [
        'displayErrorDetails' => true,
        'db_p' => $dbConfig,
    ],
];