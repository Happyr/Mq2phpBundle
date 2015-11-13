<?php

namespace Happyr\DeferredEventSimpleBusBundle\Service;

/**
 * Interface HeaderAwareInterface.
 */
interface HeaderAwareInterface
{
    public function setHeader($name, $value);
    public function getHeader($name);
}
