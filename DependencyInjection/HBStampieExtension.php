<?php

/*
 * This file is part of the HBStampieBundle package.
 *
 * (c) Henrik Bjornskov <henrik@bjrnskov.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HB\StampieBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class HBStampieExtension extends \Symfony\Component\HttpKernel\DependencyInjection\Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('config.xml');

        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration($container->getParameter('kernel.debug')), $configs);

        $mailerServiceId  = sprintf("hb_stampie.mailer.%s", $config['mailer']);

        if (!$container->has($mailerServiceId)) {
            throw new \InvalidArgumentException(sprintf('Invalid mailer "%s" specified', $config['mailer']));
        }

        if (class_exists('Symfony\Component\DependencyInjection\ChildDefinition')) {
            $definition = new ChildDefinition($mailerServiceId);
        } else {
            $definition = new DefinitionDecorator($mailerServiceId);
        }

        // get the abstract definition of an mailer and create "hb_stampie" based on it
        $definition
            ->setPublic(false)
            ->setArguments(array(
                new Reference($config['http_client']),
                $config['server_token'],
            ))
        ;

        $container->setDefinition('hb_stampie.mailer.real', $definition);

        $mailerId = 'hb_stampie.mailer.real';

        if (isset($config['extra'])) {
            $this->loadExtra($config['extra'], $container, $loader);

            $mailerId = 'hb_stampie.extra.mailer';
        }

        $container->setAlias('hb_stampie.mailer', $mailerId);
    }

    public function getAlias()
    {
        return 'hb_stampie';
    }

    private function loadExtra(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        if (!class_exists('Stampie\Extra\Mailer')) {
            throw new \InvalidArgumentException('You cannot activate the extra feature without the StampieExtra library');
        }

        $loader->load('extra.xml');

        if (!empty($config['delivery_address'])) {
            $container->getDefinition('hb_stampie.extra.listener.impersonate')
                ->replaceArgument(0, $config['delivery_address']);
        } else {
            $container->removeDefinition('hb_stampie.extra.listener.impersonate');
        }

        if (!$config['logging']) {
            $container->removeDefinition('hb_stampie.listener.message_logger');
        }
    }
}
