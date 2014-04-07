<?php

namespace Afa\Framework;

interface IResponseFactory
{
    /**
     * @param array $data
     * @return IResponse
     */
    public function createOkResponse(array $data);
}