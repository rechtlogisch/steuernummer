<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Normalize;

it('normalizes tax number', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Normalize($steuernummer, $federalState))
        ->run();

    expect($result->isValid())->toBeTrue()
        ->and($result->getOutput())->toBe($elsterSteuernummer);
})->with('tax-numbers');

it('normalizes edge cases from BE', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = (new Normalize($steuernummer, $federalState))
        ->run();

    expect($result->isValid())->toBeTrue()
        ->and($result->getOutput())->toBe($elsterSteuernummer);
})->with('tax-numbers-edge-cases-be-valid');
