<?php

declare(strict_types=1);

use Rechtlogisch\Steuernummer;

/**
 * @return string|array{
 *     steuernummer: string,
 *     federalState: string
 * }
 */
function denormalizeSteuernummer(string $elsterSteuernummer, ?string $federalState = null, bool $details = false): string|array
{
    return $details === true
        ? (new Steuernummer\Denormalize($elsterSteuernummer, $federalState))->returnWithFederalState()
        : (new Steuernummer\Denormalize($elsterSteuernummer, $federalState))->returnSteuernummerOnly();
}

function normalizeSteuernummer(string $elsterSteuernummer, string $federalState): string
{
    return (new Steuernummer\Normalize($elsterSteuernummer, $federalState))->run();
}

function validateElsterSteuernummer(string $elsterSteuernummer, ?string $federalState = null): bool
{
    return (new Steuernummer\Validate($elsterSteuernummer, $federalState))->run();
}

function validateSteuernummer(string $steuernummer, string $federalState): bool
{
    $elsterSteuernummer = (new Steuernummer\Normalize($steuernummer, $federalState))->run();

    return (new Steuernummer\Validate($elsterSteuernummer, $federalState))->run();
}
