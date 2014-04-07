<?php

namespace Afa\Framework\Symfony;

class BaseResponse implements \Afa\Framework\Http\IResponse
{
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $symfonyResponse;

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct(\Symfony\Component\HttpFoundation\Response $response)
    {
        $this->symfonyResponse = $response;
    }

    public function send()
    {
        $this->symfonyResponse->send();
    }
}