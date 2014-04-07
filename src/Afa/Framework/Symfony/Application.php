<?php

namespace Afa\Framework\Symfony;

class Application implements \Afa\Framework\IApplication
{

    protected $basePath;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    public function run()
    {
        $routesLoader = new \Symfony\Component\Routing\Loader\YamlFileLoader(new \Symfony\Component\Config\FileLocator());
        $routes = $routesLoader->load($this->basePath . '/config/routing/config.yml');

        $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $diLoader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, new \Symfony\Component\Config\FileLocator());
        $diLoader->load($this->basePath . '/config/di/config.yml');

        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

        $context = new \Symfony\Component\Routing\RequestContext();
        $context->fromRequest($request);
        $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

        $request->attributes->add($matcher->matchRequest($request));

        $resolver = new \Afa\Framework\Symfony\DiAwareControllerResolver($container);
        $controller = $resolver->getController($request);
        $arguments = $resolver->getArguments($request, $controller);

        call_user_func_array($controller, $arguments);

        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->prepare($request);
        $response->send();
    }
}