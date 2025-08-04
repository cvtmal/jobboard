<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->strict();
// arch()->preset()->laravel(); // Too restrictive when using custom request methods inside controllers
arch('security for app code')
    ->expect('App')
    ->not->toUse(['die', 'dd', 'dump', 'eval', 'exec', 'passthru', 'shell_exec', 'system', 'md5']);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();
