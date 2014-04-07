<?php

namespace Afa\Framework\Symfony;

class Application implements \Afa\Framework\IApplication
{

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var \Afa\Framework\IResponseFactory
     */
    protected $responseFactory;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        set_error_handler(array($this, 'transformErrorToException'));
        register_shutdown_function(array($this, 'shutDown'));
    }

    public function run()
    {
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $this->responseFactory = new JsonResponseFactory($request);

        try
        {
            $responseData = $this->handleRequest($request);
            $response = $this->responseFactory->createOkResponse($responseData);
        }
        catch (\Exception $e)
        {
            $response = $this->responseFactory->createServerErrorResponse(array(
                'message' => $e->getMessage(),
            ));
        }

        $response->send();
    }

    /**
     * @param int $severity
     * @param string $message
     * @param string $file
     * @param int $line
     * @param string $context
     * @throws \RuntimeException
     */
    public function transformErrorToException($severity, $message, $file, $line, $context)
    {
        throw new \RuntimeException($file . ': line ' . $line . ' - ' . $message);
    }

    public function shutDown()
    {
        $error = error_get_last();
        if (is_array($error))
        {
            $response = $this->responseFactory->createServerErrorResponse(array(
                'message' => $error['file'] . ': line ' . $error['line'] . ' - ' . $error['message'],
            ));
            $response->send();
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     * @throws \RuntimeException
     */
    protected function handleRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $routesLoader = new \Symfony\Component\Routing\Loader\YamlFileLoader(new \Symfony\Component\Config\FileLocator());
        $routes = $routesLoader->load($this->basePath . '/config/routing/config.yml');

        $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $diLoader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, new \Symfony\Component\Config\FileLocator());
        $diLoader->load($this->basePath . '/config/di/config.yml');

        $context = new \Symfony\Component\Routing\RequestContext();
        $context->fromRequest($request);
        $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

        $request->attributes->add($matcher->matchRequest($request));

        $resolver = new \Afa\Framework\Symfony\DiAwareControllerResolver($container);
        $controller = $resolver->getController($request);

        $arguments = $resolver->getArguments($request, $controller);

        $responseData = call_user_func_array($controller, $arguments);

        if (!is_array($responseData))
        {
            throw new \RuntimeException('Unexpected response from ' . $request->attributes->get('_controller') . '. Array expected.');
        }

        return $responseData;
    }
}