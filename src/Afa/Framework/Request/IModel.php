<?php

namespace Afa\Framework\Request;

interface IModel
{
    /**
     * @throws \Afa\Framework\Exception\BadRequestException
     */
    public function validate();
}