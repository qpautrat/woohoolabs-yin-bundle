<?php

namespace QP\WoohoolabsYinBundle\Factory;

use Nyholm\Psr7\Response;
use QP\WoohoolabsYinBundle\Request\Request as JsonApiRequest;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\JsonApi;

/**
 * @author Quentin <quentin.pautrat@gmail.com>
 *
 * Allows you to instantiate JsonApi class.
 */
class JsonApiFactory
{
    /**
     * @var HttpMessageFactoryInterface
     */
    private $psrFactory;

    /**
     * @var ExceptionFactoryInterface
     */
    private $exceptionFactory;

    /**
     * Constructor.
     *
     * @param HttpMessageFactoryInterface $psrFactory
     */
    public function __construct(HttpMessageFactoryInterface $psrFactory, ExceptionFactoryInterface $exceptionFactory)
    {
        $this->psrFactory = $psrFactory;
        $this->exceptionFactory = $exceptionFactory;
    }

    /**
     * Create a new instance of JsonApi by transforming a HttpFoundation Request into PSR7 Request.
     *
     * @param RequestStack $requestStack
     *
     * @return JsonApi
     */
    public function create(RequestStack $requestStack)
    {
        $request = $this->psrFactory->createRequest($requestStack->getCurrentRequest());

        return new JsonApi(new JsonApiRequest($request, $this->exceptionFactory), new Response(), $this->exceptionFactory);
    }
}
