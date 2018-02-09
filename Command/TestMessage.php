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

use Stampie\IdentityInterface;
use Stampie\Message;

/**
 * Class TestMessage.
 */
class TestMessage extends Message
{
    /**
     * @var
     */
    private $from;

    /**
     * @param IdentityInterface $to
     * @param $from
     */
    public function __construct(IdentityInterface $to, $from)
    {
        parent::__construct($to);
        $this->from = $from;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return 'This is a test subject';
    }
}
