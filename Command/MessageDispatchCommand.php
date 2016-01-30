<?php

namespace Happyr\DeferredEventSimpleBusBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class MessageDispatchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('happyr:deferred-message:dispatch')
            ->setDescription('Dispatch a message from a queue to simple bus')
            ->addArgument('queue', InputArgument::REQUIRED, 'The name of the queue')
            ->addArgument('data', InputArgument::REQUIRED, 'A serialized event to dispatch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $queueName = $input->getArgument('queue');

        $container->get('logger')->log('info', 'Consuming data from queue: '. $queueName);
        $container->get('happyr.deferred_event_simple_bus.consumer_wrapper')->consume($queueName, $input->getArgument('data'));
    }
}
