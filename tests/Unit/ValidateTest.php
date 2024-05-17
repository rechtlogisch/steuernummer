<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Dto\ValidationResult;
use Rechtlogisch\Steuernummer\Validate;

it('returns a ValidationResult on valid input', function () {
    $result = new Validate('1121081508150', 'BE');
    expect($result)->toBeObject(ValidationResult::class);
});

it('returns a ValidationResult on invalid input', function () {
    $result = new Validate('1', 'X');
    expect($result)->toBeObject(ValidationResult::class);
});

it('fails when no elsterSteuernummer was provided', function () {
    /** @phpstan-ignore-next-line */
    new Validate(null, 'BE');
})->throws(TypeError::class);

it('fails when null was provided as elsterSteuernummer', function () {
    /** @phpstan-ignore-next-line */
    new Validate(null, 'BE');
})->throws(TypeError::class);

it('does not throw an exception when no federalState was provided', function () {
    new Validate('1121081508150');
})->throwsNoExceptions();

it('returns false when the federal state is too short and federal state not provided', function () {
    $result = (new Validate('112105678901'))->run();
    expect($result->isValid())->toBeFalse();
});

it('returns false for an invalid BUFA provided in BE', function () {
    $result = (new Validate('1234012345678', 'BE'))->run();
    expect($result->isValid())->toBeFalse();
});

it('returns array as first error for an invalid BUFA provided in BE', function () {
    $result = (new Validate('1234012345678', 'BE'))->run();
    expect($result->getFirstError())
        ->toBeArray()
        ->not()->toBeEmpty();
});

it('validates correctly a tax number with checksum 0 for zweierProcedure', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($result->isValid())
        ->toBeTrue();
})->with('tax-numbers-edge-cases-zweier-procedure');
