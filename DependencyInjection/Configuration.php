<?php

namespace HB\StampieBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('hb_stampie');

        $root
            ->children()
                ->scalarNode('mailer')->isRequired()->end()
                ->scalarNode('server_token')->isRequired()->end()
                ->scalarNode('adapter')->isRequired()->end()
            ->end()
        ;

        return $builder;
    }
}
