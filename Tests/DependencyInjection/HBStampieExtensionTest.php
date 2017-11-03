<?php

namespace HB\StampieBundle\Tests\DependencyInjection;

use HB\StampieBundle\DependencyInjection\HBStampieExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class HBStampieExtensionTest extends TestCase
{
    /** @var HBStampieExtension */
    private $extension;

    protected function setUp()
    {
        $this->extension = new HBStampieExtension();
    }

    public function testWithHttplugBundle()
    {
        $container = $this->createContainerBuilder(false, array(
            'HttplugBundle' => array(
            ),
        ));

        $this->extension->load(array(
            'hb_stampie' => array(
                'mailer' => 'mail_chimp',
                'server_token' => 'token',
            ),
        ), $container);

        $this->assertTrue($container->hasAlias('hb_stampie.http_client'));
        $this->assertEquals('httplug.client.default', (string) $container->getAlias('hb_stampie.http_client'));
    }

    public function testWithoutHttplugBundle()
    {
        $container = $this->createContainerBuilder();

        $this->extension->load(array(
            'hb_stampie' => array(
                'mailer' => 'mail_chimp',
                'server_token' => 'token',
            ),
        ), $container);

        $this->assertTrue($container->hasDefinition('hb_stampie.http_client'));
    }

    public function testWithCustomHttpClient()
    {
        $container = $this->createContainerBuilder();

        $this->extension->load(array(
            'hb_stampie' => array(
                'http_client' => 'my.http_client',
                'mailer' => 'mail_chimp',
                'server_token' => 'token',
            ),
        ), $container);

        $definition = $container->getDefinition('hb_stampie.mailer.real');
        $this->assertEquals('my.http_client', (string) $definition->getArgument(0));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid mailer "DummyMailer" specified
     */
    public function testExceptionWhenInvalidMailerSpecified()
    {
        $this->extension->load(array(
            'hb_stampie' => array(
                'mailer' => 'DummyMailer',
                'server_token' => 'token',
            ),
        ), $this->createContainerBuilder());
    }

    public function testHBStampieMailerDefinitionIsBuilt()
    {
        $builder = $this->createContainerBuilder();

        $this->extension->load(array(
            'hb_stampie' => array(
                'mailer' => 'postmark',
                'server_token' => 'token',
            ),
        ), $builder);

        $this->assertTrue($builder->hasAlias('hb_stampie.mailer'));
        $this->assertEquals('hb_stampie.mailer.real', (string) $builder->getAlias('hb_stampie.mailer'));

        $this->assertTrue($builder->hasDefinition('hb_stampie.mailer.real'));
        if (class_exists('Symfony\Component\DependencyInjection\ChildDefinition')) {
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\ChildDefinition', $builder->getDefinition('hb_stampie.mailer.real'));
        } else {
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\DefinitionDecorator', $builder->getDefinition('hb_stampie.mailer.real'));
        }
        $this->assertEquals('hb_stampie.mailer.postmark', $builder->getDefinition('hb_stampie.mailer.real')->getParent());

        $this->assertEquals(array(
            new Reference('hb_stampie.http_client'),
            'token',
        ), $builder->getDefinition('hb_stampie.mailer.real')->getArguments());
    }

    public function testAlias()
    {
        $this->assertEquals('hb_stampie', $this->extension->getAlias());
    }

    public function testExtraLogging()
    {
        $builder = $this->createContainerBuilder();

        $this->extension->load(array(
            'hb_stampie' => array(
                'mailer' => 'postmark',
                'server_token' => 'token',
                'extra' => array(
                    'logging' => true,
                ),
            ),
        ), $builder);

        $this->assertTrue($builder->hasDefinition('hb_stampie.listener.message_logger'));
        $this->assertTrue($builder->hasDefinition('hb_stampie.data_collector'));
    }

    protected function createContainerBuilder($kernelDebug = false, $kernelBundles = array())
    {
        return new ContainerBuilder(new ParameterBag(array(
            'kernel.debug' => $kernelDebug,
            'kernel.bundles' => $kernelBundles,
        )));
    }
}
