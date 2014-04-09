<?php

namespace Afa\Framework\Request\Model;

interface IModelResolver
{
    /**
     * @param string $modelClass
     * @return \Afa\Framework\Request\IModel
     */
    public function resolve($modelClass);
}