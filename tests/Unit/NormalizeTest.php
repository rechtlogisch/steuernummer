<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Exceptions;
use Rechtlogisch\Steuernummer\Normalize;

it('fails when steuernummer is too short', function (string $federalState) {
    new Normalize('123456789', $federalState);
})->with('federal-states')->throws(Exceptions\InvalidSteuernummerLength::class);

it('fails when steuernummer is too long', function (string $federalState) {
    new Normalize('123456789012', $federalState);
})->with('federal-states')->throws(Exceptions\InvalidSteuernummerLength::class);

it('fails when steuernummer is too long in federal states where a 10 digit long steuernummer is being expected', function (string $federalState) {
    new Normalize('12345678901', $federalState);
})->with('federal-states-steuernummer-10-digits')->throws(Exceptions\InvalidSteuernummerLength::class);

it('fails when steuernummer is too short in federal states where a 11 digit long steuernummer is being expected', function (string $federalState) {
    new Normalize('1234567890', $federalState);
})->with('federal-states-steuernummer-11-digits')->throws(Exceptions\InvalidSteuernummerLength::class);

it('runs with int values as steuernummer', function () {
    // PHP casts int to string due to the type hint in class constructor
    // https://www.php.net/manual/en/language.types.string.php#language.types.string.casting
    /** @phpstan-ignore-next-line */
    $normalized = (new Normalize(2181508150, 'BE'))
        ->run();

    expect($normalized)->toBeString();
});

it('fails when steuernummer is not string(able)', function () {
    $input = new stdClass();
    /** @noinspection PhpParamsInspection @phpstan-ignore-next-line */
    new Normalize($input, 'XX');
})->throws(TypeError::class);

it('fails when steuernummer is null', function () {
    /** @phpstan-ignore-next-line */
    new Normalize(null, 'XX');
})->throws(TypeError::class);

it('fails when federalState is null', function () {
    /** @phpstan-ignore-next-line */
    new Normalize('1121081508150', null);
})->throws(TypeError::class);

it('fails on invalid federal states', function () {
    (new Normalize('1121081508150', 'XX'))
        ->guardFederalState();
})->throws(Exceptions\InvalidFederalState::class);

it('fails when federalState is not string(able)', function () {
    $input = new stdClass();
    /** @noinspection PhpParamsInspection @phpstan-ignore-next-line */
    new Normalize('1121081508150', $input);
})->throws(TypeError::class);
