<?php

namespace Happyr\Mq2phpBundle\Service;

use Happyr\Mq2phpBundle\Event\PrePublishMessage;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer;
use SimpleBus\Serialization\Envelope\Serializer\StandardMessageInEnvelopeSerializer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This service adds some extra headers on the message envelope.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MessageSerializerDecorator implements MessageInEnvelopSerializer, HeaderAwareInterface
{
    /**
     * @var MessageInEnvelopSerializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param MessageInEnvelopSerializer $serializer
     * @param array                      $headers
     * @param string                     $secretKey
     * @param EventDispatcherInterface   $eventDispatcher
     */
    public function __construct(
        MessageInEnvelopSerializer $serializer,
        EventDispatcherInterface $eventDispatcher,
        array $headers = array(),
        $secretKey = null
    ) {
        $this->serializer = $serializer;
        $this->eventDispatcher = $eventDispatcher;
        $this->headers = $headers;
        $this->secretKey = empty($secretKey) ? '' : $secretKey;
    }

    /**
     * Serialize a Message by wrapping it in an Envelope and serializing the envelope. This will take the
     * SimpleBus envelope and add it as body on a HTTP-like message.
     *
     * {@inheritdoc}
     */
    public function wrapAndSerialize($message)
    {
        $serializedMessage = $this->serializer->wrapAndSerialize($message);

        $message = [];
        foreach ($this->headers as $name => $value) {
            if (empty($value)) {
                continue;
            }

            $message['headers'][] = ['key' => $name, 'value' => $value];
        }
        $message['body'] = $serializedMessage;

        // Add a hash where the secret key is baked in.
        $message['headers'][] = ['key' => 'hash', 'value' => sha1($this->secretKey.$serializedMessage)];

        $event = new PrePublishMessage($message);
        $this->eventDispatcher->dispatch(PrePublishMessage::NAME, $event);

        return json_encode($event->getMessage());
    }

    /**
     * Deserialize a Message that was wrapped in an Envelope.
     *
     * {@inheritdoc}
     */
    public function unwrapAndDeserialize($serializedEnvelope)
    {
        return $this->serializer->unwrapAndDeserialize($serializedEnvelope);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     */
    public function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }

        return;
    }
}
