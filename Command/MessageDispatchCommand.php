<?php

namespace Happyr\Mq2phpBundle\Command;

use Happyr\Mq2phpBundle\Service\ConsumerWrapper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command that will be called by dispatch-message.php.
 * It will give a SimpleBus message envelope to the ConsumerWrapper.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MessageDispatchCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'happyr:mq2php:dispatch';

    /**
     * @var ConsumerWrapper
     */
    private $consumer;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @param ConsumerWrapper $consumer
     * @param string          $secretKey
     */
    public function __construct(ConsumerWrapper $consumer, $secretKey)
    {
        $this->consumer = $consumer;
        $this->secretKey = $secretKey;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Dispatch a message from a queue to simple bus')
            ->addArgument('queue', InputArgument::REQUIRED, 'The name of the queue')
            ->addArgument('data', InputArgument::REQUIRED, 'A serialized event to dispatch')
            ->addArgument('hash', InputArgument::OPTIONAL, 'A hash that could be used to verify the message is valid')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $input->getArgument('data');
        $queueName = $input->getArgument('queue');
        $hash = $input->getArgument('hash');

        if (!empty($this->secretKey)) {
            // If we have a secret key we must validate the hash
            if (!hash_equals(sha1($this->secretKey.$data), $hash)) {
                throw new \Exception('Hash verification failed');
            }
        }

        $this->consumer->consume($queueName, $data);
    }
}
