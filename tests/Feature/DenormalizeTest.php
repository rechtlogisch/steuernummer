<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Denormalize;
use Rechtlogisch\Steuernummer\Exceptions\InvalidBufa;

it('denormalizes tax number without provided federalState', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $denormalized = (new Denormalize($elsterSteuernummer))
        ->returnSteuernummerOnly();

    expect($denormalized)
        ->toBeString()
        ->toBe($steuernummer);
})->with('tax-numbers');

it('denormalizes tax number with provided federalState', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $denormalized = (new Denormalize($elsterSteuernummer, $federalState))
        ->returnSteuernummerOnly();

    expect($denormalized)
        ->toBeString()
        ->toBe($steuernummer);
})->with('tax-numbers');

it('denormalizes tax number and return result together with federalState', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Denormalize($elsterSteuernummer))
        ->returnWithFederalState();

    expect($result)
        ->toBeArray()
        ->and($result['steuernummer'])
        ->toBe($steuernummer)
        ->and($result['federalState'])
        ->toBe($federalState);
})->with('tax-numbers');

it('denormalizes edge cases from BE', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $denormalized = (new Denormalize($elsterSteuernummer, $federalState))
        ->returnSteuernummerOnly();

    expect($denormalized)
        ->toBeString()
        ->toBe($steuernummer);
})->with('tax-numbers-edge-cases-be-valid');

it('returns errors when steuernummer with not whitelisted bufa is being tried to be denormalized', function () {
    $denormalized = (new Denormalize('1100081508150', 'BE'))
        ->run();

    expect($denormalized->isValid())->toBeFalse()
        ->and($denormalized->getErrors())->not()->toBeEmpty()
        ->and($denormalized->getFirstErrorKey())->toBe(InvalidBufa::class)
        ->and($denormalized->getOutput())->toBeNull();
});
