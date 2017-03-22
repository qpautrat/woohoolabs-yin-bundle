<?php

namespace QP\WoohoolabsYinBundle\DependencyInjection;

use QP\WoohoolabsYinBundle\Request\ParamConverter\JsonApiParamConverter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Quentin Pautrat <quentin.pautrat@gmail.com>
 */
class QPWoohoolabsYinExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (array_key_exists('SensioFrameworkExtraBundle', $container->getParameter('kernel.bundles'))) {
            $definition = new Definition(JsonApiParamConverter::class, [new Reference('qp_woohoolabs_yin.json_api.factory')]);
            $definition->setTags(['request.param_converter' => [[]]]);
            $container->setDefinition('qp_woohoolabs_yin.json_api.param_converter', $definition);
        }

        $container->setAlias('qp_woohoolabs_yin.exception_factory', $config['exception_factory']);
    }
}
