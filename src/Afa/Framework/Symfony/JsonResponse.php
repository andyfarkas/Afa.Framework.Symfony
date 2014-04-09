<?php

namespace Afa\Framework\Symfony;

class JsonResponse implements \Afa\Framework\IResponse
{
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $symfonyResponse;

    /**
     * @param \Symfony\Component\HttpFoundation\JsonResponse $response
     */
    public function __construct(\Symfony\Component\HttpFoundation\JsonResponse $response)
    {
        $this->symfonyResponse = $response;
    }

    public function send()
    {
        $this->symfonyResponse->send();
    }
}