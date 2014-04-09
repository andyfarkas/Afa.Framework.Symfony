<?php

require_once dirname(__DIR__ ) . '/vendor/autoload.php';


class MyController
{
    public function helloAction(Model $model)
    {

    }

}

class Model implements \Afa\Framework\Request\IModel
{
    /**
     * @throws \Afa\Framework\Exception\BadRequestException
     */
    public function validate()
    {
        throw new \Afa\Framework\Exception\BadRequestException('Hands off mister!');
    }
}

$app = new \Afa\Framework\Symfony\Application(dirname(__DIR__));
$app->run();


