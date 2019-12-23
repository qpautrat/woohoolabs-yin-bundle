<?php

namespace QP\WoohoolabsYinBundle\Factory;

use Psr\Http\Message\ResponseFactoryInterface;
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
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    /**
     * @var ExceptionFactoryInterface
     */
    private $exceptionFactory;

    /**
     * Constructor.
     */
    public function __construct(
        HttpMessageFactoryInterface $psrFactory,
        ResponseFactoryInterface $responseFactory,
        ExceptionFactoryInterface $exceptionFactory
    ) {
        $this->psrFactory = $psrFactory;
        $this->responseFactory = $responseFactory;
        $this->exceptionFactory = $exceptionFactory;
    }

    /**
     * Create a new instance of JsonApi by transforming a HttpFoundation Request into PSR7 Request.
     *
     * @return JsonApi
     */
    public function create(RequestStack $requestStack)
    {
        $request = $this->psrFactory->createRequest($requestStack->getCurrentRequest());
        $response = $this->responseFactory->createResponse();

        return new JsonApi(
            new JsonApiRequest(
                $request,
                $this->exceptionFactory
            ),
            $response,
            $this->exceptionFactory
        );
    }
}
