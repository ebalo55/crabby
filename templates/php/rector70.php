<?php

declare(strict_types = 1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRootFiles()
    ->withPaths(
        [
            __DIR__ . '/7.x',
        ],
    )
    // uncomment to reach your current PHP version
    ->withPhpSets(php70: true);
