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
        return $this->createResponseWithCode($data, 200);
    }

    protected function createResponseWithCode(array $data, $code)
    {
        $symfonyResponse = new \Symfony\Component\HttpFoundation\JsonResponse($data, $code);
        $symfonyResponse->prepare($this->request);
        $response = new BaseResponse($symfonyResponse);
        return $response;
    }

    public function createServerErrorResponse(array $data)
    {
        return $this->createResponseWithCode($data, 500);
    }

}