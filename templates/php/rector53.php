<?php

declare(strict_types = 1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRootFiles()
    ->withPaths(
        [
            __DIR__ . '/5.x',
        ],
    )
    // uncomment to reach your current PHP version
    ->withPhpSets(php53: true);
