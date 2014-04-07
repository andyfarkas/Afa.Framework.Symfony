<?php

require_once dirname(__DIR__ ) . '/vendor/autoload.php';


class MyController
{
    public function helloAction(\Afa\Framework\IRequest $request)
    {
        var_dump($request);
    }

}

$app = new \Afa\Framework\Symfony\Application(dirname(__DIR__));
$app->run();


