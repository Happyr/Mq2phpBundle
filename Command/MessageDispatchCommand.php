<?php

namespace Happyr\Mq2phpBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command that will be called by dispatch-message.php.
 * It will give a SimpleBus message envelope to the ConsumerWrapper.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MessageDispatchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('happyr:mq2php:dispatch')
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
        $secretKey = $this->getContainer()->getParameter('happyr.mq2php.secret_key');

        if (!empty($secretKey)) {
            // If we have a secret key we must validate the hash
            if (!hash_equals(sha1($secretKey.$data), $hash)) {
                throw new \Exception('Hash verification failed');
            }
        }

        $this->getContainer()->get('happyr.mq2php.consumer_wrapper')->consume($queueName, $data);
    }
}
