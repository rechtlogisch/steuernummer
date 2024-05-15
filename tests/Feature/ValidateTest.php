<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Exceptions;
use Rechtlogisch\Steuernummer\Validate;

it('validates tax number', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer))
        ->run();

    expect($result)
        ->toBeBool()
        ->toBeTrue();
})->with('tax-numbers');

it('validates tax number with provided federalState', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($result)
        ->toBeBool()
        ->toBeTrue();
})->with('tax-numbers');

it('throws an exception when incorrect federalState provided', function () {
    $elsterSteuernummer = '1121081508150'; // from BE
    (new Validate($elsterSteuernummer, 'NW'))
        ->run();
})->throws(Exceptions\InvalidBufa::class);

it('validates edge cases from BE', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($result)
        ->toBeBool()
        ->toBeTrue();
})->with('tax-numbers-edge-cases-be-valid');

it('return false for invalid elsterSteuernummer', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($result)
        ->toBeBool()
        ->toBeFalse();
})->with('tax-numbers-invalid');

it('return false for elsterSteuernummer edge cases from BE', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Validate($elsterSteuernummer, $federalState))
        ->run();

    expect($result)
        ->toBeBool()
        ->toBeFalse();
})->with('tax-numbers-edge-cases-be-invalid');
