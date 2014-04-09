<?php

namespace Afa\Framework\Request\Model;

class ModelResolver implements IModelResolver
{

    /**
     * @var \Afa\Framework\IRequest
     */
    protected $request;

    /**
     * @param \Afa\Framework\IRequest $request
     */
    public function __construct(\Afa\Framework\IRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $modelClass
     * @return \Afa\Framework\Request\IModel
     */
    public function resolve($modelClass)
    {
        $model = new $modelClass($this->request);
        return $model;
    }
}