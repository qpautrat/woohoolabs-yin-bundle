<?php

namespace QPautrat\WoohoolabsYinBundle\DependencyInjection;

use QPautrat\WoohoolabsYinBundle\Request\ParamConverter\JsonApiParamConverter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Quentin Pautrat <quentin.pautrat@gmail.com>
 */
class QPautratWoohoolabsYinExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        if (array_key_exists('SensioFrameworkExtraBundle', $container->getParameter('kernel.bundles'))) {
            $definition = new Definition(JsonApiParamConverter::class, [new Reference('qpautrat_woohoolabs_yin.json_api.factory')]);
            $definition->setTags(['request.param_converter' => [[]]]);
            $container->setDefinition('qpautrat_woohoolabs_yin.json_api.param_converter', $definition);
        }
    }
}
