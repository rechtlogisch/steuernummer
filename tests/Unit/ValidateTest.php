<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Exceptions;
use Rechtlogisch\Steuernummer\Validate;

it('fails when no elsterSteuernummer was provided', function () {
    /** @phpstan-ignore-next-line */
    new Validate(null, 'XX');
})->throws(TypeError::class);

it('does not throw an exception when no federalState was provided', function () {
    new Validate('1121081508150');

    expect(1)->toBe(1);
});

it('fails when the federal state is too short and federal state not provided', function () {
    new Validate('1');
})->throws(Exceptions\InvalidElsterSteuernummerLength::class);

it('fails when an invalid BUFA provided in BE', function () {
    new Validate('1234012345678', 'BE');
})->throws(Exceptions\InvalidBufa::class);

it('validates correctly a tax number with checksum 0 for zweierProcedure', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $validated = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($validated)
        ->toBeTrue();
})->with('tax-numbers-edge-cases-zweier-procedure');
