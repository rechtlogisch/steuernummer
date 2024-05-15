<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use Rechtlogisch\Steuernummer\Common;
use Rechtlogisch\Steuernummer\Exceptions;

afterEach(function () {
    putenv('STEUERNUMMER_PRODUCTION=');
});

it('fails for test bufa with federal when environment is production', function (string $federalState, string $bufa) {
    putenv('STEUERNUMMER_PRODUCTION=true');
    (new Common($bufa, $federalState))
        ->guardBufa();
})->with('bufas-test-valid')->throws(Exceptions\InvalidBufa::class);

it('does not throw exception for test bufa with federal state when environment is not production', function (string $federalState, string $bufa) {
    putenv('STEUERNUMMER_PRODUCTION=false');
    (new Common($bufa, $federalState))
        ->guardBufa();
})->with('bufas-test-valid')->throwsNoExceptions();

it('fails for test bufa without federal state when environment is production', function (string $bufa) {
    putenv('STEUERNUMMER_PRODUCTION=true');
    (new Common($bufa))
        ->guardBufa();
})->with(['3098'])->throws(Exceptions\InvalidBufa::class);

it('does not throw exception for test bufa without federal state when environment is not production', function (string $bufa) {
    putenv('STEUERNUMMER_PRODUCTION=false');
    (new Common($bufa))
        ->guardBufa();
})->with(['3098'])->throwsNoExceptions();

it('fails for non existing test bufa without federal state when environment is not production', function (string $bufa) {
    putenv('STEUERNUMMER_PRODUCTION=false');
    (new Common($bufa))
        ->guardBufa();
})->with(['7777'])->throws(Exceptions\InvalidBufa::class);
