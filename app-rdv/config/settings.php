<?php

$dbrConfig = parse_ini_file(__DIR__ . '/rdv.ini', true);
$dbrConfig = $dbrConfig ? $dbrConfig['database'] : [];

return [
    'settings' => [
        'displayErrorDetails' => true,
        'db_rdv' => $dbrConfig,
    ],
];