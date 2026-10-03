<?php

namespace App\Logging;

use Monolog\Handler\FilterHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class SplitByLevel
{
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('split');
        $days = $config['days'] ?? 14;

        foreach (['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'] as $level) {
            $monologLevel = Logger::toMonologLevel($level);

            $handler = new RotatingFileHandler(
                storage_path("logs/{$level}.log"),
                $days
            );

            // min and max the same = only this level
            $logger->pushHandler(new FilterHandler($handler, $monologLevel, $monologLevel));
        }

        return $logger;
    }
}
