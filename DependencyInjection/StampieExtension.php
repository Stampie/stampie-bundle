<?php

/*
 * This file is part of the StampieBundle package.
 *
 * (c) Henrik Bjornskov <henrik@bjrnskov.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stampie\StampieBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class StampieExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('config.xml');

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration($container->getParameter('kernel.debug')), $configs);

        $mailerServiceId = sprintf('stampie.mailer.%s', $config['mailer']);

        if (!$container->has($mailerServiceId)) {
            throw new \InvalidArgumentException(sprintf('Invalid mailer "%s" specified', $config['mailer']));
        }

        if (class_exists('Symfony\Component\DependencyInjection\ChildDefinition')) {
            $definition = new ChildDefinition($mailerServiceId);
        } else {
            $definition = new DefinitionDecorator($mailerServiceId);
        }

        // get the abstract definition of an mailer and create "stampie" based on it
        $definition
            ->setPublic(true)
            ->setArguments([
                new Reference($config['http_client']),
                $config['server_token'],
            ]);

        $container->setDefinition('stampie.mailer', $definition);

        if ($config['extra']['enabled']) {
            $this->loadExtra($config['extra'], $container, $loader);
        }
    }

    private function loadExtra(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        if (!class_exists('Stampie\Extra\Mailer')) {
            throw new \InvalidArgumentException('You cannot activate the extra feature without the StampieExtra library');
        }

        $loader->load('extra.xml');

        if (!empty($config['delivery_address'])) {
            $container->getDefinition('stampie.extra.listener.impersonate')
                ->replaceArgument(0, $config['delivery_address']);
        } else {
            $container->removeDefinition('stampie.extra.listener.impersonate');
        }

        if (!$config['logging']) {
            $container->removeDefinition('stampie.listener.message_logger');
        }
    }
}
