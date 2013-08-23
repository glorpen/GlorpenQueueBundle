<?php

namespace Glorpen\QueueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('glorpen_queue');

        $rootNode
        ->children()
        	->scalarNode("backend")
        		->isRequired()
        		->cannotBeEmpty()
        	->end()
        ->end()
        ;

        return $treeBuilder;
    }
}
