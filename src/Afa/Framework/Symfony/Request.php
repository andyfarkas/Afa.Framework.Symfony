<?php

namespace Afa\Framework\Symfony;

class Request implements \Afa\Framework\IRequest
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $symfonyRequest;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->symfonyRequest = $request;
    }

    /**
     * @param string $key
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function get($key)
    {
        return $this->symfonyRequest->get($key);
    }
}