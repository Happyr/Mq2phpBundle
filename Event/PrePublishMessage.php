<?php

namespace Happyr\Mq2phpBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatch a message before we publish it. Event listeners may modify the message.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class PrePublishMessage extends Event
{
    const NAME = 'happyr.mq2php.pre_publish_message';

    /**
     * This is the json message before we run json_encode.
     *
     * @var array
     */
    private $message;

    /**
     * The class/type from the original message (command/event).
     *
     * @var string
     */
    private $type;

    /**
     * @param array  $message
     * @param string $type
     */
    public function __construct(array $message, $type)
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param array $message
     *
     * @return PrePublishMessage
     */
    public function setMessage(array $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
