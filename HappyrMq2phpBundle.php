<?php

namespace Happyr\Mq2phpBundle;

use Happyr\Mq2phpBundle\DependencyInjection\Compiler\RegisterConsumers;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HappyrMq2phpBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterConsumers());
    }
}
