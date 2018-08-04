<?php

namespace Stampie\StampieBundle\Tests\Functional;

use Nyholm\BundleTest\BaseBundleTestCase;
use Stampie\Mailer\MailGun;
use Stampie\StampieBundle\StampieBundle;

class BundleInitializationTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return StampieBundle::class;
    }

    public function testInitBundle()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/config.yml');

        // Boot the kernel.
        $this->bootKernel();

        // Get the container
        $container = $this->getContainer();

        $this->assertTrue($container->has('stampie.mailer'));
        $service = $container->get('stampie.mailer');
        $this->assertInstanceOf(MailGun::class, $service);
    }
}
