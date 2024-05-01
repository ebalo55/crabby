<?php

declare(strict_types = 1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRootFiles()
    ->withPaths(
        [
            __DIR__ . '/8.x',
        ],
    )
    // uncomment to reach your current PHP version
    ->withPhpSets(php80: true);
