<?php

namespace Afa\Framework;

interface IRequestFactory
{
    /**
     * @return IRequest
     */
    public function createRequestFromGlobals();
}