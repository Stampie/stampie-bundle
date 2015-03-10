<?php

/*
 * This file is part of the HBStampieBundle package.
 *
 * (c) Henrik Bjornskov <henrik@bjrnskov.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HB\StampieBundle;
use HB\StampieBundle\DependencyInjection\Compiler\AdapterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class HBStampieBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AdapterCompilerPass());
    }
}
