<?php
header('Content-Type: application/json');

$info = [
    'php_version' => PHP_VERSION,
    'display_errors' => ini_get('display_errors'),
    'error_reporting' => ini_get('error_reporting'),
    'log_errors' => ini_get('log_errors'),
    'error_log' => ini_get('error_log'),
    'output_buffering' => ini_get('output_buffering'),
    'implicit_flush' => ini_get('implicit_flush')
];

echo json_encode($info);
?> 