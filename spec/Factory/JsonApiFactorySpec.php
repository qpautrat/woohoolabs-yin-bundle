<?php

namespace spec\QPautrat\WoohoolabsYinBundle\Factory;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ServerRequestInterface;
use QPautrat\WoohoolabsYinBundle\Factory\JsonApiFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request;
use WoohooLabs\Yin\JsonApi\JsonApi;

class JsonApiFactorySpec extends ObjectBehavior
{
    public function let(DiactorosFactory $factory)
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(JsonApiFactory::class);
    }

    public function it_creates_jsonapi(Request $request, ServerRequestInterface $psrRequest, $factory)
    {
        $factory->createRequest($request)->willReturn($psrRequest);
        $factory->createRequest($request)->shouldBeCalled();

        $this->create($request)->shouldReturnAnInstanceOf(JsonApi::class);
    }
}
