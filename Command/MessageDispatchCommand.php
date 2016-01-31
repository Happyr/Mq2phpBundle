<?php

namespace Happyr\Mq2phpBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command that wil be called by dispatch-message.php.
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('happyr.mq2php.consumer_wrapper')->consume(
            $input->getArgument('queue'),
            $input->getArgument('data')
        );
    }
}
