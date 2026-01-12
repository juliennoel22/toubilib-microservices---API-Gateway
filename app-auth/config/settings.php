<?php

$dbConfig = parse_ini_file(__DIR__ . '/praticien.ini', true)['database'];
$dbrConfig = parse_ini_file(__DIR__ . '/rdv.ini', true)['database'];
$dbaConfig = parse_ini_file(__DIR__ . '/auth.ini', true)['database'];
$dbpConfig = parse_ini_file(__DIR__ . '/patient.ini', true)['database'];

return [
    'settings' => [
        'displayErrorDetails' => true,
        'db_auth' => $dbaConfig,
    ],
];