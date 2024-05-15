<?php

test('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'echo', 'print_r'])
    ->not->toBeUsed();

test('dtos are final')
    ->expect('Rechtlogisch\Steuernummer\Dto')
    ->toBeFinal();

test('use strict mode')
    ->expect('Rechtlogisch\Steuernummer')
    ->toUseStrictTypes();
