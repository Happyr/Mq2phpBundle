<?php

namespace Happyr\Mq2phpBundle\Tests\Functional\app\Service;

use SimpleBus\Asynchronous\Publisher\Publisher;

class DummyPublisher implements Publisher
{
    public function publish($message)
    {
    }
}
