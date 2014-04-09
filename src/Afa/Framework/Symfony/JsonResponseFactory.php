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

    /**
     * @param array $data
     * @param int $code
     * @return JsonResponse
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function createResponseWithCode(array $data, $code)
    {
        $symfonyResponse = new \Symfony\Component\HttpFoundation\JsonResponse($data, $code);
        $symfonyResponse->prepare($this->request);
        $response = new JsonResponse($symfonyResponse);
        return $response;
    }

    /**
     * @param array $data
     * @return \Afa\Framework\IResponse|JsonResponse
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function createServerErrorResponse(array $data)
    {
        return $this->createResponseWithCode($data, 500);
    }

    /**
     * @param array $data
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return \Afa\Framework\IResponse
     */
    public function createNotFoundResponse(array $data)
    {
        return $this->createResponseWithCode($data, 404);
    }

    /**
     * @param array $data
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return \Afa\Framework\IResponse
     */
    public function createBadRequestResponse(array $data)
    {
        return $this->createResponseWithCode($data, 400);
    }
}