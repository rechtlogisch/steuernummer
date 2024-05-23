<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Exceptions;
use Rechtlogisch\Steuernummer\Validate;

it('exports elsterSteuernummer from datasets', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = file_put_contents('tests/Datasets/input-test.txt', $elsterSteuernummer.PHP_EOL, FILE_APPEND);
    expect($result)
        ->toBeInt();
})->group('manual')
    ->skip('only to export elsterSteuernummer for further testing')
    ->with('tax-number-example');
//    ->with('tax-numbers');
//    ->with('tax-numbers-edge-cases-zweier-procedure');
//    ->with('tax-numbers-edge-cases-be-valid');
//    ->with('tax-numbers-invalid');
//    ->with('tax-numbers-edge-cases-be-invalid');

it('generates a csv for comparison with eric result', function () {
    $path = 'tests/Datasets/';
    $filename = 'input-dummy.txt';
    $lines = file($path.$filename);
    if ($lines === false) {
        exit("Error reading {$filename}".PHP_EOL);
    }
    $result = [];

    foreach ($lines as $line) {
        $elsterSteuernummer = trim($line);
        if ($elsterSteuernummer === '') {
            continue;
        }

        $resultValidation = (new Validate($elsterSteuernummer))
            ->run();

        $codeMap = [
            Exceptions\InvalidElsterSteuernummerCheckDigit::class => '610001034',
            Exceptions\FederalStateCouldNotBeDetermined::class => '610001035',
            Exceptions\InvalidElsterSteuernummerLength::class => '610001035',
            Exceptions\InvalidBufa::class => '610001038',
        ];

        $code = ($resultValidation->isValid() === true)
            ? '0'
            : $codeMap[$resultValidation->getFirstErrorKey()];

        $textResultValidation = ($resultValidation->isValid() === true)
            ? 'valid'
            : 'invalid';

        $text = "{$elsterSteuernummer},{$textResultValidation},{$code}".PHP_EOL;

        $result[] = $text;
    }

    $resultSaving = file_put_contents($path.'result-dummy.csv', implode('', $result));

    expect($resultSaving)
        ->toBeInt();
})->group('manual');
