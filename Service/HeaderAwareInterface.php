<?php

namespace Happyr\DeferredEventSimpleBusBundle\Service;

interface HeaderAwareInterface
{
    /**
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value);

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getHeader($name);
}
