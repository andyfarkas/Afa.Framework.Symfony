<?php

namespace Afa\Framework\Symfony;

class DiAwareControllerResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var \Afa\Framework\Request\Model\IModelResolver
     */
    protected $modelResolver;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     * @param \Afa\Framework\Request\Model\IModelResolver $modelResolver
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    public function __construct(\Symfony\Component\DependencyInjection\Container $container,
                                \Afa\Framework\Request\Model\IModelResolver $modelResolver,
                                \Symfony\Component\HttpKernel\Log\LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->container = $container;
        $this->modelResolver = $modelResolver;
    }

    /**
     * @param string $controller
     * @return array|mixed
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Exception
     * @throws \Symfony\Component\DependencyInjection\Exception\InactiveScopeException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode('::', $controller, 2);

        $controller = $this->container->get($class);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        return array($controller, $method);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $symfonyRequest
     * @param $controller
     * @param array $parameters
     * @return array
     * @throws \RuntimeException
     */
    protected function doGetArguments(\Symfony\Component\HttpFoundation\Request $symfonyRequest, $controller, array $parameters)
    {
        $attributes = $symfonyRequest->attributes->all();
        $arguments = array();
        $request = new Request($symfonyRequest);
        foreach ($parameters as $param) {
            if (array_key_exists($param->name, $attributes)) {
                $arguments[] = $attributes[$param->name];
            } elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
                $arguments[] = $request;
            } elseif ($param->getClass()->implementsInterface('Afa\Framework\Request\IModel')) {
                $arguments[] = $this->modelResolver->resolve($param->getClass()->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                if (is_array($controller)) {
                    $repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
                } elseif (is_object($controller)) {
                    $repr = get_class($controller);
                } else {
                    $repr = $controller;
                }

                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->name));
            }
        }

        return $arguments;
    }
}