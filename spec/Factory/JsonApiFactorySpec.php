<?php

namespace spec\QP\WoohoolabsYinBundle\Factory;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use QP\WoohoolabsYinBundle\Factory\JsonApiFactory;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\JsonApi;

class JsonApiFactorySpec extends ObjectBehavior
{
    public function let(
        HttpMessageFactoryInterface $psr7Factory,
        RequestStack $requestStack,
        Request $request,
        ServerRequestInterface $psrRequest,
        ResponseFactoryInterface $responseFactory,
        ExceptionFactoryInterface $exceptionFactory
    ) {
        $this->beConstructedWith($psr7Factory, $responseFactory, $exceptionFactory);
        $requestStack->getCurrentRequest()->willReturn($request);
        $psr7Factory->createRequest($request)->willReturn($psrRequest);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(JsonApiFactory::class);
    }

    public function it_creates_jsonapi(
        RequestStack $requestStack,
        Request $request,
        HttpMessageFactoryInterface $psr7Factory,
        ResponseFactoryInterface $responseFactory,
        $exceptionFactory
    ) {
        $psr7Factory->createRequest($request)->shouldBeCalled();
        $responseFactory->createResponse()->shouldBeCalled();
        $this->create($requestStack)->shouldReturnAnInstanceOf(JsonApi::class);
        $this->create($requestStack)->getExceptionFactory()->shouldReturn($exceptionFactory);
    }
}
