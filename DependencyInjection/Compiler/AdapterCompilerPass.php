<?php

/*
 * This file is part of the HBStampieBundle package.
 *
 * (c) Henrik Bjornskov <henrik@bjrnskov.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HB\StampieBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AdapterCompilerPass
 * @package HB\StampieBundle\DependencyInjection\Compiler
 */
class AdapterCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $adapterMapping = array(
            'buzz' =>'hb_stampie.adapter.buzz',
            'guzzle.client' => 'hb_stampie.adapter.guzzle'
        );

        $this->addAdapterIfAvailable($container, $adapterMapping);
    }


    /**
     * @param ContainerBuilder $container
     * @param array $adapterMapping
     */
    private function addAdapterIfAvailable(ContainerBuilder $container, array $adapterMapping)
    {
        foreach ($adapterMapping as $serviceId => $adapterId) {
            if ($container->has($serviceId)) {
                $container->getDefinition($adapterId)
                    ->replaceArgument(0, new Reference($serviceId));
            }
        }
    }
}
