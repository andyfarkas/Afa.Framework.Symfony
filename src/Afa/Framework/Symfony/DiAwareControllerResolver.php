<?php

namespace Afa\Framework\Symfony;

class DiAwareControllerResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    public function __construct(\Symfony\Component\DependencyInjection\Container $container, \Symfony\Component\HttpKernel\Log\LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->container = $container;
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
}