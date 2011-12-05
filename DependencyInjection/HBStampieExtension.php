<?php

namespace HB\StampieBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class HBStampieExtension extends \Symfony\Component\HttpKernel\DependencyInjection\Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new Filelocator(__DIR__ . '/../Resources/config'));
        $loader->load('config.xml');

        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $configs);

        $adapterServiceId = sprintf('hb_stampie.adapter.%s', $config['adapter']);
        $mailerServiceId  = sprintf("hb_stampie.mailer.%s", $config['mailer']);

        if (!$container->has($adapterServiceId)) {
            throw new \InvalidArgumentException(sprintf('Invalid adapter "%s" specified', $config['adapter']));
        }

        if (!$container->has($mailerServiceId)) {
            throw new \InvalidArgumentException(sprintf('Invalid mailer "%s" specified', $config['mailer']));
        }

        // get the abstract definition of an mailer and create "hb_stampie" based on it
        $container
            ->setDefinition('hb_stampie.mailer', new DefinitionDecorator($mailerServiceId))
            ->setArguments(array(
                $container->getDefinition($adapterServiceId),
                $config['server_token'],
            ))
        ;
    }

    public function getAlias()
    {
        return 'hb_stampie';
    }
}
