<?php

namespace Happyr\DeferredEventSimpleBusBundle;

use Happyr\DeferredEventSimpleBusBundle\DependencyInjection\CompilerPass\UpdateSimpleBusAlias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HappyrDeferredEventSimpleBusBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new UpdateSimpleBusAlias());
    }
}
