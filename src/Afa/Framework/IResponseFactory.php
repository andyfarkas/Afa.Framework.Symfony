<?php

namespace Afa\Framework;

interface IResponseFactory
{
    /**
     * @param array $data
     * @return IResponse
     */
    public function createOkResponse(array $data);

    /**
     * @param array $data
     * @return IResponse
     */
    public function createServerErrorResponse(array $data);

    /**
     * @param array $data
     * @return IResponse
     */
    public function createNotFoundResponse(array $data);

    /**
     * @param array $data
     * @return IResponse
     */
    public function createBadRequestResponse(array $data);

}