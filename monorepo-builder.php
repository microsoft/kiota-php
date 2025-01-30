<?php

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\MonorepoBuilder\Config\MBConfig;

require_once __DIR__ . '/vendor/autoload.php';

return static function (MBConfig $mbConfig): void {
    // where are the packages located?
    $mbConfig->packageDirectories([
        __DIR__ . '/packages/',
    ]);
    $mbConfig->dataToRemove([
        ComposerJsonSection::MINIMUM_STABILITY => Option::REMOVE_COMPLETELY,
        ComposerJsonSection::REPOSITORIES => [
            Option::REMOVE_COMPLETELY,
        ],
        ComposerJsonSection::REPLACE => [
            Option::REMOVE_COMPLETELY,
        ]
    ]);
};
