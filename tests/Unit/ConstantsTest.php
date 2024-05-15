<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Constants;

it('list validation procedures with federal state names', function () {
    $result = Constants::listValidationProcedures();

    ksort($result);

    // print_r($result);

    expect($result)->toBeArray();
});

it('groups validation procedures by method name', function () {
    $result = Constants::groupValidationProcedures();

    // print_r($result);

    expect($result)->toBeArray();
});

it('groups factors by factor digits', function () {
    $result = Constants::groupFactors();

    // print_r($result);

    expect($result)->toBeArray();
});

it('lists federal states names with iso code keys', function () {
    $result = Constants::federalStatesNames();

    // print_r($result);

    expect($result)->toBeArray();
});

it('lists federal states codes', function () {
    $result = Constants::federalStatesCodes();

    // print_r(implode(', ', $result));

    expect($result)->toBeArray();
});

it('lists federal states codes with steuernummer 10 digits', function () {
    $result = Constants::FEDERAL_STATES_STEUERNUMMER_10_DIGITS;

    // print_r(implode(', ', $result));

    expect($result)
        ->toBeArray()
        ->toBe(['BE', 'BW', 'HB', 'HH', 'NI', 'RP', 'SH']);
});

it('lists federal states codes with steuernummer 11 digits', function () {
    $result = Constants::federalStatesSteuernummer11Digits();

    // print_r(implode(', ', $result));

    expect(array_values($result))
        ->toBeArray()
        ->toBe(['BB', 'BY', 'HE', 'MV', 'NW', 'SL', 'SN', 'ST', 'TH']);
});
