<?php

namespace Afa\Framework\Symfony;

class JsonResponseFactory implements \Afa\Framework\IResponseFactory
{

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $data
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @return \Afa\Framework\IResponse
     */
    public function createOkResponse(array $data)
    {
        $symfonyResponse = new \Symfony\Component\HttpFoundation\JsonResponse($data);
        $symfonyResponse->prepare($this->request);
        $response = new \Afa\Framework\Symfony\Response\BaseResponse($symfonyResponse);
        return $response;
    }
}