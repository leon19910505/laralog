<?php

namespace Leon19910505\Laralog;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\LogManager as LaravelLogManager;

class LaraLogger extends LaravelLogManager
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }
}
