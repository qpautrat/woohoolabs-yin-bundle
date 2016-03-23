<?php

namespace QPautrat\WoohoolabsYinBundle\Factory;

use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactory;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Request\Request as JsonApiRequest;
use Zend\Diactoros\Response;

/**
 * @author Quentin <quentin.pautrat@gmail.com>
 *
 * Allows you to instantiate JsonApi class.
 */
class JsonApiFactory
{
    /**
     * Constructor.
     *
     * @param DiactorosFactory $factory
     */
    public function __construct(DiactorosFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Create a new instance of JsonApi by transforming a HttpFoundation Request into PSR7 Request.
     *
     * @param Request $request
     *
     * @return JsonApi
     */
    public function create(Request $request)
    {
        $request = $this->factory->createRequest($request);

        return new JsonApi(new JsonApiRequest($request), new Response(), new ExceptionFactory());
    }
}
