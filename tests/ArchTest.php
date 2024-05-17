<?php

test('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'echo', 'print_r'])
    ->not->toBeUsed();

test('dtos are final')
    ->expect('Rechtlogisch\Steuernummer\Dto')
    ->toBeFinal();

test('abstracts are abstract')
    ->expect('Rechtlogisch\Steuernummer\Abstracts')
    ->toBeAbstract();

test('use strict mode')
    ->expect('Rechtlogisch\Steuernummer')
    ->toUseStrictTypes();
