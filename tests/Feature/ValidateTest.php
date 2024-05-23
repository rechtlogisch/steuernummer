<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Validate;

it('validates tax number', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer))
        ->run();

    expect($result->isValid())->toBeTrue();
})->with('tax-numbers');

it('validates tax number with provided federalState', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($result->isValid())->toBeTrue();
})->with('tax-numbers');

it('returns false and an error when incorrect federalState provided', function () {
    $elsterSteuernummer = '1121081508150'; // from BE
    $result = (new Validate($elsterSteuernummer, 'NW'))
        ->run();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstError())->toContain('BUFA 1121 is not supported in federalState NW');
});

it('validates edge cases from BE', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($result->isValid())->toBeTrue();
})->with('tax-numbers-edge-cases-be-valid');

it('return false for invalid elsterSteuernummer', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($result->isValid())->toBeFalse();
})->with('tax-numbers-invalid');

it('return false for elsterSteuernummer edge cases from BE', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($result->isValid())->toBeFalse();
})->with('tax-numbers-edge-cases-be-invalid');
