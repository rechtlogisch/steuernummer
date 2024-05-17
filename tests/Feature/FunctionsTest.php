<?php

it('normalizes a steuernummer with the global normalizeSteuernummer() function', function () {
    $result = normalizeSteuernummer('21/815/08150', 'BE');

    expect($result)
        ->toBeString()
        ->toBe('1121081508150');
});

it('denormalizes an elster-steuernummer with the global denormalizeSteuernummer() function', function () {
    $result = denormalizeSteuernummer('1121081508150');

    expect($result)->toBe('21/815/08150');
});

it('denormalizes an elster-steuernummer with the global denormalizeSteuernummer() function and returns details', function () {
    /** @var array{steuernummer: string, federalState: string} $result */
    $result = denormalizeSteuernummer('1121081508150', details: true);
    expect($result)
        ->toBeArray()
        ->and($result['steuernummer'])->toBe('21/815/08150')
        ->and($result['federalState'])->toBe('BE');
});

it('validates an elster-steuernummer with the global validateElsterSteuernummer() function', function () {
    $result = validateElsterSteuernummer('1121081508150');
    expect($result->isValid())->toBeTrue();
});

it('checks if a elster steuernummer is valid with global isElsterSteuernummerValid() and without a federal state function', function () {
    $result = isElsterSteuernummerValid('1121081508150');
    expect($result)->toBeTrue();
});

it('checks if a elster steuernummer is valid with global isElsterSteuernummerValid() and with a federal state function', function () {
    $result = isElsterSteuernummerValid('1121081508150', 'BE');
    expect($result)->toBeTrue();
});

it('validates a steuernummer with the global validateSteuernummer() function', function () {
    $result = validateSteuernummer('21/815/08150', 'BE');
    expect($result->isValid())->toBeTrue()
        ->and($result->getErrors())->toBeEmpty();
});

it('checks if a steuernummer is valid with global isSteuernummerValid() function', function () {
    $result = isSteuernummerValid('21/815/08150', 'BE');
    expect($result)->toBeTrue();
});
