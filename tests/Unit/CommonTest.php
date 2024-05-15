<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Common;
use Rechtlogisch\Steuernummer\Constants;
use Rechtlogisch\Steuernummer\Exceptions;

it('passes on valid federal states', function (string $federalState) {
    $common = new Common(null, $federalState);
    $common->guardFederalState();

    expect(1)->toBe(1);
})->with(['BB', 'BE', 'BW', 'BY', 'HB', 'HE', 'HH', 'MV', 'NI', 'NW', 'RP', 'SH', 'SL', 'SN', 'ST', 'TH']);

it('fails on invalid federal states', function () {
    (new Common(null, 'XX'))
        ->guardFederalState();
})->throws(Exceptions\InvalidFederalState::class);

it('passes on elsterSteuernummer with correct syntax', function () {
    $common = new Common('1234012345678', 'XX');
    $common->guardElsterSteuernummer();

    expect(1)->toBe(1);
});

it('fails when elsterSteuernummer contains non-digits', function () {
    (new Common('X', 'XX'))
        ->guardElsterSteuernummer();
})->throws(Exceptions\ElsterSteuernummerCanContainOnlyDigits::class);

it('fails when elsterSteuernummer is too short', function () {
    (new Common('123401234567', 'XX'))
        ->guardElsterSteuernummer();
})->throws(Exceptions\InvalidElsterSteuernummerLength::class);

it('fails when elsterSteuernummer is too long', function () {
    (new Common('12340123456789', 'XX'))
        ->guardElsterSteuernummer();
})->throws(Exceptions\InvalidElsterSteuernummerLength::class);

it('fails when the formatKey is not zero', function () {
    (new Common('1234912345678', 'XX'))
        ->guardElsterSteuernummer();
})->throws(Exceptions\InvalidElsterSteuernummerFormatKey::class);

it('fails when an invalid district is being provided', function (string $federalState) {
    $districtsNotAllowed = ['000', '998', '999'];

    foreach ($districtsNotAllowed as $district) {
        $suffix = '45678';
        if ($federalState === 'NW') {
            $district = str_pad($district, Constants::FEDERAL_STATES_DETAILS['NW']['districtLength'], '0', STR_PAD_LEFT);
            $suffix = '5678';
        }

        $elsterSteuernummer = '12340'.$district.$suffix;

        (new Common($elsterSteuernummer, $federalState))
            ->guardElsterSteuernummer();
    }
})->with('federal-states')->throws(Exceptions\InvalidDistrict::class);

it('fails when an invalid district is being provided in NW', function () {
    $districtsNotAllowed = ['0000', '0998', '0999'];

    foreach ($districtsNotAllowed as $district) {
        $elsterSteuernummer = '12340'.$district.'5678';

        (new Common($elsterSteuernummer, 'NW'))
            ->guardElsterSteuernummer();
    }
})->throws(Exceptions\InvalidDistrict::class);

it('fails when an invalid district lower than 100 is being provided in specific federal states', function (string $federalState) {
    $district = '099';
    $elsterSteuernummer = '12340'.$district.'45678';
    (new Common($elsterSteuernummer, $federalState))
        ->guardElsterSteuernummer();
})->with(['BB', 'BY', 'MV', 'SL', 'SN', 'ST', 'TH'])->throws(Exceptions\InvalidDistrict::class);

it('fails when unplausible unique numbers and checksum is provided in NW', function () {
    $unplausible = '0001';
    $elsterSteuernummer = '123401234'.$unplausible;
    (new Common($elsterSteuernummer, 'NW'))
        ->guardElsterSteuernummer();
})->throws(Exceptions\InvalidElsterSteuernummer::class);

it('fails when a not supported bufa is provided', function (string $federalState, string $bufa) {
    (new Common($bufa, $federalState))
        ->guardBufa();
})->with('bufas-invalid')->throws(Exceptions\InvalidBufa::class);

it('passes when a supported bufa is provided', function (string $federalState, string $bufa) {
    (new Common($bufa, $federalState))
        ->guardBufa();

    expect(1)->toBe(1);
})->with('bufas-valid');
