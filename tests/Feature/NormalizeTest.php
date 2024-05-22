<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Exceptions\InvalidBufa;
use Rechtlogisch\Steuernummer\Normalize;

it('normalizes tax number', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Normalize($steuernummer, $federalState))
        ->run();

    expect($result->isValid())->toBeTrue()
        ->and($result->getOutput())->toBe($elsterSteuernummer);
})->with('tax-numbers');

it('normalizes tax number returning only elster steuernummer', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Normalize($steuernummer, $federalState))
        ->returnElsterSteuernummerOnly();

    expect($result)->toBeString()
        ->toBe($elsterSteuernummer);
})->with('tax-numbers');

it('normalizes edge cases from BE', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Normalize($steuernummer, $federalState))
        ->run();

    expect($result->isValid())->toBeTrue()
        ->and($result->getOutput())->toBe($elsterSteuernummer);
})->with('tax-numbers-edge-cases-be-valid');

it('returns errors when steuernummer with not whitelisted bufa is being tried to be normalized', function () {
    $result = (new Normalize('00/815/08150', 'BE'))
        ->run();

    expect($result->isValid())->toBeFalse()
        ->and($result->getErrors())->not()->toBeEmpty()
        ->and($result->getFirstErrorKey())->toBe(InvalidBufa::class)
        ->and($result->getOutput())->toBeNull();
});
