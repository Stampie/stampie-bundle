<?php

namespace HB\StampieBundle\Tests\DependencyInjection;

use HB\StampieBundle\DependencyInjection\HBStampieExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class HBStampieExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->extension = new HBStampieExtension();
    }

    public function testExceptionWhenInvalidAdapterSpecified()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid adapter "DummyAdapter" specified');
        $this->extension->load(array(
            'hb_stampie' => array(
                'adapter' => 'DummyAdapter',
                'mailer' => 'DummyMailer',
                'server_token' => 'token',
            ),
        ), new ContainerBuilder());
    }

    public function testExceptionWhenInvalidMailerSpecified()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid mailer "DummyMailer" specified');
        $this->extension->load(array(
            'hb_stampie' => array(
                'adapter' => 'buzz',
                'mailer' => 'DummyMailer',
                'server_token' => 'token',
            ),
        ), new ContainerBuilder());
    }

    public function testHBStampieMailerDefinitionIsBuilt()
    {
        $builder = new ContainerBuilder();

        $this->extension->load(array(
            'hb_stampie' => array(
                'adapter' => 'buzz',
                'mailer' => 'postmark',
                'server_token' => 'token',
            ),
        ), $builder);

        $this->assertTrue($builder->hasDefinition('hb_stampie.mailer'));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\DefinitionDecorator', $builder->getDefinition('hb_stampie.mailer'));
        $this->assertEquals('hb_stampie.mailer.postmark', $builder->getDefinition('hb_stampie.mailer')->getParent());

        $this->assertEquals(array(
            $builder->getDefinition('hb_stampie.adapter.buzz'),
            'token',
        ), $builder->getDefinition('hb_stampie.mailer')->getArguments()); 
    }

    public function testAlias()
    {
        $this->assertEquals('hb_stampie', $this->extension->getAlias());
    }
}
