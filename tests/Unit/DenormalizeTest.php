<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Denormalize;
use Rechtlogisch\Steuernummer\Exceptions;

it('fails when elsterSteuernummer is too short', function (string $federalState) {
    new Denormalize('123456789012', $federalState);
})->with('federal-states')->throws(Exceptions\InvalidElsterSteuernummerLength::class);

it('fails when the federal state is too short and federal state not provided', function () {
    new Denormalize('1');
})->throws(Exceptions\InvalidElsterSteuernummerLength::class);

it('fails when steuernummer is too long', function (string $federalState) {
    new Denormalize('12345678901234', $federalState);
})->with('federal-states')->throws(Exceptions\InvalidElsterSteuernummerLength::class);

it('runs with int values as elsterSteuernummer', function () {
    // PHP casts int to string due to the type hint in class constructor
    // https://www.php.net/manual/en/language.types.string.php#language.types.string.casting
    /** @phpstan-ignore-next-line */
    $denormalized = (new Denormalize(1121081508150, 'BE'))
        ->returnSteuernummerOnly();

    expect($denormalized)->toBeString();
});

it('fails when elsterSteuernummer is not string(able)', function () {
    $input = new stdClass();
    /** @noinspection PhpParamsInspection @phpstan-ignore-next-line */
    new Denormalize($input, 'XX');
})->throws(TypeError::class);

it('fails when elsterSteuernummer is null', function () {
    /** @phpstan-ignore-next-line */
    new Denormalize(null, 'XX');
})->throws(TypeError::class);

it('fails on invalid federal states', function () {
    (new Denormalize('1121081508150', 'XX'))
        ->guardFederalState();
})->throws(Exceptions\InvalidFederalState::class);

it('fails when federalState is not string(able)', function () {
    $input = new stdClass();
    /** @noinspection PhpParamsInspection @phpstan-ignore-next-line */
    new Denormalize('1121081508150', $input);
})->throws(TypeError::class);
