<?php

require_once dirname(__DIR__ ) . '/vendor/autoload.php';

class MyController
{
    public function helloAction()
    {
        return new \Symfony\Component\HttpFoundation\JsonResponse('hello');
    }

    public function worldAction()
    {
        return new \Symfony\Component\HttpFoundation\JsonResponse('world');
    }
}

$container = new Symfony\Component\DependencyInjection\Container();


$routesLoader = new \Symfony\Component\Routing\Loader\YamlFileLoader(new \Symfony\Component\Config\FileLocator());
$routes = $routesLoader->load(dirname(__DIR__) . '/config/routing/config.yml');

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$context = new \Symfony\Component\Routing\RequestContext();
$context->fromRequest($request);
$matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

$request->attributes->add($matcher->matchRequest($request));

$resolver = new \Symfony\Component\HttpKernel\Controller\ControllerResolver();
$controller = $resolver->getController($request);
$arguments = $resolver->getArguments($request, $controller);

call_user_func_array($controller, $arguments);

$response = new \Symfony\Component\HttpFoundation\JsonResponse();
$response->prepare($request);
$response->send();


