<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Denormalize;
use Rechtlogisch\Steuernummer\Dto\DenormalizationResult;
use Rechtlogisch\Steuernummer\Exceptions;

it('checks the happy path', function () {
    $input = '1121081508150';
    $result = (new Denormalize($input, 'BE'))
        ->run();

    expect($result)->toBeInstanceOf(DenormalizationResult::class)
        ->and($result->isValid())->toBeTrue()
        ->and($result->getInput())->toBeString()->toBe($input)
        ->and($result->getOutput())->toBeString()->toBe('21/815/08150')
        ->and($result->getFederalState())->toBeString()->toBe('BE')
        ->and($result->getErrors())->toBeEmpty();
});

it('fails when elsterSteuernummer is too short', function (string $federalState) {
    $result = (new Denormalize('123456789012', $federalState))
        ->run();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InvalidElsterSteuernummerLength::class);
})->with('federal-states');

it('fails when the federal state is too short and federal state not provided', function () {
    $result = (new Denormalize('1'))
        ->run();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InvalidElsterSteuernummerLength::class);
});

it('fails when steuernummer is too long', function (string $federalState) {
    $result = (new Denormalize('12345678901234', $federalState))
        ->run();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InvalidElsterSteuernummerLength::class);
})->with('federal-states');

it('returns only denormalized string', function () {
    $denormalized = (new Denormalize('1121081508150', 'BE'))
        ->returnSteuernummerOnly();

    expect($denormalized)->toBeString();
});

it('returns only denormalized string when valid elsterSteuernummer as int provided', function () {
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
