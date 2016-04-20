<?php

namespace QP\WoohoolabsYinBundle\Request\ParamConverter;

use QP\WoohoolabsYinBundle\Factory\JsonApiFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use WoohooLabs\Yin\JsonApi\JsonApi;

/**
 * @author Quentin <quentin.pautrat@gmail.com>
 *
 * Requires SensioExtraFramworkBundle
 *
 * Allows you to get an instance of JsonApi directly in a Controller
 */
class JsonApiParamConverter implements ParamConverterInterface
{
    /**
     * Constrcutor.
     *
     * @param JsonApiFactory $factory
     */
    public function __construct(JsonApiFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $request->attributes->set($configuration->getName(), $this->factory->create($request));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === JsonApi::class;
    }
}
