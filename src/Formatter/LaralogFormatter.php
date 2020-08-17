<?php

namespace Leon19910505\Laralog\Formatter;

use Monolog\Logger;

class LaralogFormatter
{
    /**
     * @param Logger $logger
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new JsonFormatter());
        }
    }
}
