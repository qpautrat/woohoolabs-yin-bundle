<?php

namespace spec\QPautrat\WoohoolabsYinBundle\Request\ParamConverter;

use PhpSpec\ObjectBehavior;
use QPautrat\WoohoolabsYinBundle\Factory\JsonApiFactory;
use QPautrat\WoohoolabsYinBundle\Request\ParamConverter\JsonApiParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use WoohooLabs\Yin\JsonApi\JsonApi;

class JsonApiParamConverterSpec extends ObjectBehavior
{
    public function let(JsonApiFactory $factory)
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(JsonApiParamConverter::class);
    }

    public function it_should_support(ParamConverter $configuration)
    {
        $configuration->getClass()->willReturn(JsonApi::class);
        $this->supports($configuration)->shouldReturn(true);

        $configuration->getClass()->willReturn(stdClass::class);
        $this->supports($configuration)->shouldReturn(false);
    }
}
