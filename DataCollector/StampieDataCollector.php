<?php

namespace HB\StampieBundle\DataCollector;

use HB\StampieBundle\EventListener\MessageLogger;
use Stampie\IdentityInterface;
use Stampie\Message\TaggableInterface;
use Stampie\MessageInterface;
use Stampie\Util\IdentityUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class StampieDataCollector extends DataCollector
{
    protected $messageLogger;

    public function __construct(MessageLogger $messageLogger = null)
    {
        $this->messageLogger = $messageLogger;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $messages = array();

        if (null !== $this->messageLogger) {
            foreach ($this->messageLogger->getMessages() as $message) {
                $messages[] = $this->normalizeMessage($message);
            }
        }

        $this->data['messages'] = $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'stampie';
    }

    public function getMessages()
    {
        return $this->data['messages'];
    }

    protected static function normalizeMessage(MessageInterface $message)
    {
        $normalizedMessage = array(
            'from' => static::normalizeIdentity($message->getFrom()),
            'to' => static::normalizeIdentity($message->getTo()),
            'cc' => static::normalizeIdentity($message->getCc()),
            'bcc' => static::normalizeIdentity($message->getBcc()),
            'replyTo' => static::normalizeIdentity($message->getReplyTo()),
            'fromRendered' => IdentityUtils::buildIdentityString($message->getFrom()),
            'toRendered' => IdentityUtils::buildIdentityString($message->getTo()),
            'ccRendered' => IdentityUtils::buildIdentityString($message->getCc()),
            'bccRendered' => IdentityUtils::buildIdentityString($message->getBcc()),
            'replyToRendered' => IdentityUtils::buildIdentityString($message->getReplyTo()),
            'subject' => $message->getSubject(),
            'headers' => $message->getHeaders(),
            'html' => $message->getHtml(),
            'text' => $message->getText(),
        );

        if ($message instanceof TaggableInterface) {
            $normalizedMessage['tag'] = $message->getTag();
        }

        return $normalizedMessage;
    }

    protected static function normalizeIdentity($identity)
    {
        if ($identity instanceof IdentityInterface) {
            return array(
                'email' => $identity->getEmail(),
                'name' => $identity->getName(),
            );
        }

        if (is_array($identity)) {
            foreach ($identity as &$currentIdentity) {
                $currentIdentity = static::normalizeIdentity($currentIdentity);
            }
        }

        return $identity;
    }
}
