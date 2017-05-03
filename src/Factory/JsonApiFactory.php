<?php

namespace QP\WoohoolabsYinBundle\Factory;

use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
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
     * @var DiactorosFactory
     */
    private $psrFactory;

    /**
     * @var ExceptionFactoryInterface
     */
    private $exceptionFactory;

    /**
     * Constructor.
     *
     * @param DiactorosFactory $psrFactory
     */
    public function __construct(DiactorosFactory $psrFactory, ExceptionFactoryInterface $exceptionFactory)
    {
        $this->psrFactory       = $psrFactory;
        $this->exceptionFactory = $exceptionFactory;
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
        $request = $this->psrFactory->createRequest($request);

        return new JsonApi(new JsonApiRequest($request, $this->exceptionFactory), new Response(), $this->exceptionFactory);
    }
}
