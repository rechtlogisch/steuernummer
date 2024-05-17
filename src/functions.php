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

function normalizeSteuernummer(string $elsterSteuernummer, string $federalState): Steuernummer\Dto\NormalizationResult
{
    return (new Steuernummer\Normalize($elsterSteuernummer, $federalState))->run();
}

function validateElsterSteuernummer(string $elsterSteuernummer, ?string $federalState = null): Steuernummer\Dto\ValidationResult
{
    return (new Steuernummer\Validate($elsterSteuernummer, $federalState))->run();
}

// @TODO: isElsterSteuernummerValid(): bool

function validateSteuernummer(string $steuernummer, string $federalState): Steuernummer\Dto\ValidationResult
{
    $elsterSteuernummer = (new Steuernummer\Normalize($steuernummer, $federalState))->run();

    return (new Steuernummer\Validate((string) $elsterSteuernummer->getOutput(), $federalState))->run();
}

// @TODO: isSteuernummerValid(): bool
