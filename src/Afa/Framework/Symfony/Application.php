<?php

namespace Afa\Framework\Symfony;

class Application implements \Afa\Framework\IApplication
{

    /**
     * @var string
     */
    protected $appDirectory;

    /**
     * @var \Afa\Framework\IResponseFactory
     */
    protected $responseFactory;

    /**
     * @param string $appDirectory
     */
    public function __construct($appDirectory)
    {
        $this->appDirectory = $appDirectory;
        set_error_handler(array($this, 'transformErrorToException'));
        register_shutdown_function(array($this, 'shutDown'));
    }

    public function run()
    {
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $this->responseFactory = new JsonResponseFactory($request);

        try
        {
            $response = $this->handleRequest($request);
        }
        catch (\Afa\Framework\Exception\NotFoundException $e)
        {
            $response = $this->responseFactory->createNotFoundResponse(array(
                'message' => $e->getMessage(),
            ));
        }
        catch (\Afa\Framework\Exception\BadRequestException $e)
        {
            $response = $this->responseFactory->createBadRequestResponse(array(
                'message' => $e->getMessage(),
            ));
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
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException
     * @throws \Exception
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \UnexpectedValueException
     * @throws \Afa\Framework\Exception\BadRequestException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    protected function handleRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $routesLoader = new \Symfony\Component\Routing\Loader\YamlFileLoader(new \Symfony\Component\Config\FileLocator());
        $routes = $routesLoader->load($this->appDirectory . '/config/routing/config.yml');

        $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $fileLocator = new \Symfony\Component\Config\FileLocator();
        $phpFileLoader = new \Symfony\Component\DependencyInjection\Loader\PhpFileLoader($container, $fileLocator);
        $yamlFileLoader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $fileLocator);
        $loaderResolver = new \Symfony\Component\Config\Loader\LoaderResolver(array($yamlFileLoader, $phpFileLoader));
        $diLoader = new \Symfony\Component\Config\Loader\DelegatingLoader($loaderResolver);
        $diLoader->load($this->appDirectory . '/config/di/config.yml');

        $context = new \Symfony\Component\Routing\RequestContext();
        $context->fromRequest($request);
        $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

        try
        {
            $request->attributes->add($matcher->matchRequest($request));
        }
        catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e)
        {
            throw new \Afa\Framework\Exception\BadRequestException('Requested resource is not valid.');
        }
        catch (\Symfony\Component\Routing\Exception\MethodNotAllowedException $e)
        {
            throw new \Afa\Framework\Exception\BadRequestException('Wrong HTTP method call.');
        }

        $modelResolver = new \Afa\Framework\Request\Model\ModelResolver(new Request($request));
        $resolver = new \Afa\Framework\Symfony\DiAwareControllerResolver($container, $modelResolver);

        try
        {
            $controller = $resolver->getController($request);
        }
        catch(\InvalidArgumentException $e)
        {
            throw new \Afa\Framework\Exception\BadRequestException('Requested resource is not valid.');
        }

        $arguments = $resolver->getArguments($request, $controller);
        $response = call_user_func_array($controller, $arguments);

        return $response;
    }
}