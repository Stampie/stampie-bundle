<?php

/*
 * This file is part of the StampieBundle package.
 *
 * (c) Henrik Bjornskov <henrik@bjrnskov.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stampie\StampieBundle\Command;

use Stampie\Identity;
use Stampie\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SendEmailCommand.
 */
class SendEmailCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('stampie:test')
            ->setDescription('Send a test email with the default mailer')
            ->addArgument('to', InputArgument::REQUIRED, 'The email address to send the test mail to')
            ->addArgument('from', InputArgument::OPTIONAL, 'The "from" email address', 'from@example.com');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $to = $input->getArgument('to');
        $from = $input->getArgument('from');

        /** @var MailerInterface $mailer */
        $mailer = $this->getContainer()->get('stampie.mailer');
        $mailerClass = new \ReflectionClass($mailer);

        $output->writeln(sprintf('Sending message from <info>%s</info> to <info>%s</info> using <info>%s</info> mailer',
            $from, $to, $mailerClass->getShortName()));

        $identity = new Identity($to);
        $message = new TestMessage($identity, $from);
        $message->setText('This is a test message');
        $mailer->send($message);
        $output->writeln(sprintf('Message successfully sent to <info>%s</info>', $to));
    }
}
