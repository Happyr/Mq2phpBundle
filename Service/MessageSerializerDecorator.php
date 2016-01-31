<?php

namespace Happyr\Mq2phpBundle\Service;

use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer;
use SimpleBus\Serialization\Envelope\Serializer\StandardMessageInEnvelopeSerializer;

/**
 * This service adds some extra headers on the message envelope.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MessageSerializerDecorator implements MessageInEnvelopSerializer, HeaderAwareInterface
{
    /**
     * @var StandardMessageInEnvelopeSerializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $headers;

    /**
     * @param StandardMessageInEnvelopeSerializer $serializer
     * @param array                               $headers
     */
    public function __construct(StandardMessageInEnvelopeSerializer $serializer, array $headers = array())
    {
        $this->serializer = $serializer;
        $this->headers = $headers;
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

        $message = '';
        foreach ($this->headers as $name => $value) {
            if (empty($value)) {
                continue;
            }

            $message .= sprintf("%s: %s\n", $name, $value);
        }
        $message .= "\n".$serializedMessage;

        return $message;
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
