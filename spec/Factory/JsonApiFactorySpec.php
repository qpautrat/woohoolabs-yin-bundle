<?php

namespace spec\QP\WoohoolabsYinBundle\Factory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use QP\WoohoolabsYinBundle\Factory\JsonApiFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Exception;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;

class JsonApiFactorySpec extends ObjectBehavior
{
    public function let(DiactorosFactory $psr7Factory, Request $request, ServerRequestInterface $psrRequest, ExceptionFactoryInterface $exceptionFactory)
    {
        $this->beConstructedWith($psr7Factory, $exceptionFactory);
        $psr7Factory->createRequest($request)->willReturn($psrRequest);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(JsonApiFactory::class);
    }

    public function it_creates_jsonapi($request, $psr7Factory, $exceptionFactory)
    {
        $psr7Factory->createRequest($request)->shouldBeCalled();
        $this->create($request)->shouldReturnAnInstanceOf(JsonApi::class);
        $this->create($request)->getExceptionFactory()->shouldReturn($exceptionFactory);
    }
}
