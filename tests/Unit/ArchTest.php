<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->strict();
arch()->preset()->laravel();
arch()->preset()->security()->ignoring('sha1'); // sha1 is used in my own VerifyEmailController, which is a copy of the one from Laravel vendor

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();
