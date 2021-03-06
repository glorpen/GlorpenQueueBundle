<?php

namespace Glorpen\QueueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Arkadiusz Dzięgiel
 */
class GlorpenQueueExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $backend = 'glorpen.queue.backend.'.$config['backend'];
        if(!$container->has($backend)){
        	throw new InvalidConfigurationException(sprintf('Backend %s was not found', $backend));
        }
        $container->setAlias('glorpen.queue.backend', $backend);
        
    }
}
