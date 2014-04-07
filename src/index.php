<?php

require_once dirname(__DIR__ ) . '/vendor/autoload.php';

$app = new \Afa\Framework\Symfony\Application(dirname(__DIR__));
$app->run();


