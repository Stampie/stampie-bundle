<?php

namespace HB\StampieBundle\Tests\Functional;

use HB\StampieBundle\HBStampieBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Stampie\Mailer\MailGun;

class BundleInitializationTest extends BaseBundleTestCase
{

    protected function setUp()
    {
        parent::setUp();

        $this->addCompilerPass(new PublicServicePass('|hb_stampie.*|'));
    }

    protected function getBundleClass()
    {
        return HBStampieBundle::class;
    }

    public function testInitBundle()
    {

        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/config.yml');

        // Boot the kernel.
        $this->bootKernel();

        // Get the container
        $container = $this->getContainer();

        $this->assertTrue($container->has('hb_stampie.mailer.real'));
        $service = $container->get('hb_stampie.mailer.real');
        $this->assertInstanceOf(MailGun::class, $service);
    }

}
