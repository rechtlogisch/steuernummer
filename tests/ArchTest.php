<?php

test('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'echo', 'print_r'])
    ->not->toBeUsed()
    ->group('arch');

test('dtos are final')
    ->expect('Rechtlogisch\Steuernummer\Dto')
    ->toBeFinal()
    ->group('arch');

test('abstracts are abstract')
    ->expect('Rechtlogisch\Steuernummer\Abstracts')
    ->toBeAbstract()
    ->group('arch');

test('use strict mode')
    ->expect('Rechtlogisch\Steuernummer')
    ->toUseStrictTypes()
    ->group('arch');
