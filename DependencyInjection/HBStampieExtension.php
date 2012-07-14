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
            ->setDefinition('hb_stampie.mailer.real', new DefinitionDecorator($mailerServiceId))
            ->setPublic(false)
            ->setArguments(array(
                $container->getDefinition($adapterServiceId),
                $config['server_token'],
            ))
        ;
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
                ->setAbstract(false)
                ->replaceArgument(0, $config['delivery_address']);
        }
    }
}
