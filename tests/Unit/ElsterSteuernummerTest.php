<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Exceptions;
use Rechtlogisch\Steuernummer\Validate;

it('exports elsterSteuernummer from datasets', function (string $federalState, string $steuernummer, string $elsterSteuernummer) {
    $result = file_put_contents('tests/Datasets/input.txt', $elsterSteuernummer.PHP_EOL, FILE_APPEND);
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

        try {
            $resultValidation = (new Validate($elsterSteuernummer))
                ->run();
            $code = ($resultValidation) ? '0' : '610001034';
        } catch (Exceptions\FederalStateCouldNotBeDetermined|Exceptions\InvalidElsterSteuernummerLength) {
            $resultValidation = false;
            $code = '610001035';
        } catch (Exceptions\InvalidBufa) {
            $resultValidation = false;
            $code = '610001038';
        }

        $textResultValidation = ($resultValidation) ? 'valid' : 'invalid';

        $text = "{$elsterSteuernummer},{$textResultValidation},{$code}".PHP_EOL;

        $result[] = $text;
    }

    $resultSaving = file_put_contents($path.'result-dummy.csv', implode('', $result));

    expect($resultSaving)
        ->toBeInt();
})->group('manual')
    ->skip('only for manual quality and performance testing');
