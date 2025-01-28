<?php

use Symplify\MonorepoBuilder\Config\MBConfig;

require_once __DIR__ . '/vendor/autoload.php';

return static function (MBConfig $mbConfig): void {
    // where are the packages located?
    $mbConfig->packageDirectories([
        __DIR__ . '/packages',
    ]);
};
