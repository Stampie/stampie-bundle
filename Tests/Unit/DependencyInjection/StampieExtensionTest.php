<?php

namespace Stampie\StampieBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Stampie\StampieBundle\DependencyInjection\StampieExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class StampieExtensionTest extends TestCase
{
    /** @var StampieExtension */
    private $extension;

    protected function setUp(): void
    {
        $this->extension = new StampieExtension();
    }

    public function testWithCustomHttpClient()
    {
        $container = $this->createContainerBuilder();

        $this->extension->load([
            'stampie' => [
                'http_client' => 'my.http_client',
                'mailer' => 'postmark',
                'server_token' => 'token',
            ],
        ], $container);

        $definition = $container->getDefinition('stampie.mailer');
        $this->assertEquals('my.http_client', (string) $definition->getArgument(0));
    }

    public function testExceptionWhenInvalidMailerSpecified()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid mailer "DummyMailer" specified');
        $this->extension->load([
            'stampie' => [
                'mailer' => 'DummyMailer',
                'server_token' => 'token',
            ],
        ], $this->createContainerBuilder());
    }

    public function testStampieMailerDefinitionIsBuilt()
    {
        $builder = $this->createContainerBuilder();

        $this->extension->load([
            'stampie' => [
                'mailer' => 'postmark',
                'server_token' => 'token',
                'extra' => false,
            ],
        ], $builder);

        $this->assertTrue($builder->hasDefinition('stampie.mailer'));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ChildDefinition', $builder->getDefinition('stampie.mailer'));
        $this->assertEquals('stampie.mailer.postmark', $builder->getDefinition('stampie.mailer')->getParent());

        $this->assertEquals([
            new Reference('httplug.client'),
            'token',
        ], $builder->getDefinition('stampie.mailer')->getArguments());
    }

    public function testAlias()
    {
        $this->assertEquals('stampie', $this->extension->getAlias());
    }

    public function testExtraLogging()
    {
        $builder = $this->createContainerBuilder();

        $this->extension->load([
            'stampie' => [
                'mailer' => 'postmark',
                'server_token' => 'token',
                'extra' => [
                    'logging' => true,
                ],
            ],
        ], $builder);

        $this->assertTrue($builder->hasDefinition('stampie.listener.message_logger'));
        $this->assertTrue($builder->hasDefinition('stampie.data_collector'));
    }

    protected function createContainerBuilder($kernelDebug = false, $kernelBundles = [])
    {
        return new ContainerBuilder(new ParameterBag([
            'kernel.debug' => $kernelDebug,
            'kernel.bundles' => $kernelBundles,
        ]));
    }
}
