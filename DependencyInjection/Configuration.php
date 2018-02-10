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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class Configuration implements ConfigurationInterface
{
    private $debug;

    /**
     * @param bool $debug The kernel.debug value
     */
    public function __construct($debug)
    {
        $this->debug = (bool) $debug;
    }

    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('stampie');

        $root
            ->children()
                ->scalarNode('mailer')->isRequired()->end()
                ->scalarNode('server_token')->isRequired()->end()
                ->scalarNode('http_client')->defaultValue('httplug.client')->end()
                ->arrayNode('extra')
                    ->children()
                        ->scalarNode('delivery_address')->defaultNull()->end()
                        ->scalarNode('logging')->defaultValue($this->debug)->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
