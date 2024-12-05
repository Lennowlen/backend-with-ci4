<?php
require 'vendor/autoload.php';

$openapi = \OpenApi\Generator::scan([__DIR__ . '/app/Controllers']);

header('Content-Type: application/json');
echo $openapi->toJson();