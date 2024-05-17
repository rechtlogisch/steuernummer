<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Dto\NormalizationResult;
use Rechtlogisch\Steuernummer\Exceptions;
use Rechtlogisch\Steuernummer\Normalize;

it('checks the happy path', function () {
    $input = '2181508150';
    $result = (new Normalize($input, 'BE'))
        ->run();

    expect($result)->toBeInstanceOf(NormalizationResult::class)
        ->and($result->isValid())->toBeTrue()
        ->and($result->getInput())->toBeString()->toBe($input)
        ->and($result->getOutput())->toBeString()->toBe('1121081508150')
        ->and($result->getErrors())->toBeEmpty();
});

it('fails when steuernummer is too short', function (string $federalState) {
    $result = (new Normalize('123456789', $federalState))
        ->run();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InvalidSteuernummerLength::class);
})->with('federal-states');

it('fails when steuernummer is too long', function (string $federalState) {
    $result = (new Normalize('123456789012', $federalState))
        ->run();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InvalidSteuernummerLength::class);
})->with('federal-states');

it('fails when steuernummer is too long in federal states where a 10 digit long steuernummer is being expected', function (string $federalState) {
    $result = (new Normalize('12345678901', $federalState))
        ->run();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InvalidSteuernummerLength::class);
})->with('federal-states-steuernummer-10-digits');

it('fails when steuernummer is too short in federal states where a 11 digit long steuernummer is being expected', function (string $federalState) {
    $result = (new Normalize('1234567890', $federalState))
        ->run();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InvalidSteuernummerLength::class);
})->with('federal-states-steuernummer-11-digits');

it('runs with int values as steuernummer', function () {
    // PHP casts int to string due to the type hint in class constructor
    // https://www.php.net/manual/en/language.types.string.php#language.types.string.casting
    $input = 2181508150;
    /** @phpstan-ignore-next-line */
    $result = (new Normalize($input, 'BE'))
        ->run();

    expect($result->isValid())->toBeTrue()
        ->and($result->getInput())->toBeString()->toBe((string) $input)
        ->and($result->getOutput())->toBeString()->toBe('1121081508150')
        ->and($result->getErrors())->toBeEmpty();
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
