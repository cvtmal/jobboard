<?php

declare (strict_types=1);
namespace RectorPrefix202503;

use Rector\Config\RectorConfig;
return static function (RectorConfig $rectorConfig) : void {
    $rectorConfig->rules([\Rector\Doctrine\Collection22\Rector\CriteriaOrderingConstantsDeprecationRector::class]);
};
