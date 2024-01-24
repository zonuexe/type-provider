<?php

use Rector\Config\RectorConfig;
use zonuexe\TypeProvider\Rector\ClassTypeProviderRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ClassTypeProviderRector::class);
    $rectorConfig->paths([
        __DIR__ . '/Book.php',
    ]);
};
