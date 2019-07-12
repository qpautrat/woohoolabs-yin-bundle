<?php

namespace QP\WoohoolabsYinBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('qp_woohoolabs_yin');

        $rootNode
            ->children()
                ->scalarNode('exception_factory')
                    ->defaultValue('qp_woohoolabs_yin.exception_factory.default')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
