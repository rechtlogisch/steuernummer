<?php

declare(strict_types=1);

use Rechtlogisch\Steuernummer;

/**
 * @return null|string|array{
 *     steuernummer: string|null,
 *     federalState: string|null
 * }
 */
function denormalizeSteuernummer(string $elsterSteuernummer, ?string $federalState = null, bool $returnWithFederalState = false): null|string|array
{
    return $returnWithFederalState === true
        ? (new Steuernummer\Denormalize($elsterSteuernummer, $federalState))->returnWithFederalState()
        : (new Steuernummer\Denormalize($elsterSteuernummer, $federalState))->returnSteuernummerOnly();
}

function normalizeSteuernummer(string $steuernummer, string $federalState): ?string
{
    return (new Steuernummer\Normalize($steuernummer, $federalState))->run()->getOutput();
}

function validateElsterSteuernummer(string $elsterSteuernummer, ?string $federalState = null): Steuernummer\Dto\ValidationResult
{
    return (new Steuernummer\Validate($elsterSteuernummer, $federalState))->run();
}

function isElsterSteuernummerValid(string $elsterSteuernummer, ?string $federalState = null): ?bool
{
    return validateElsterSteuernummer($elsterSteuernummer, $federalState)->isValid();
}

function validateSteuernummer(string $steuernummer, string $federalState): Steuernummer\Dto\ValidationResult
{
    $elsterSteuernummer = (new Steuernummer\Normalize($steuernummer, $federalState))->run();

    if ($elsterSteuernummer->isValid() !== true) {
        $validationResult = (new Steuernummer\Dto\ValidationResult);
        $validationResult->setValid(false);
        $errors = $elsterSteuernummer->getErrors() ?? [];
        if (! empty($errors)) {
            foreach ($errors as $type => $error) {
                $validationResult->addError($type, $error);
            }
        }

        return $validationResult;
    }

    return (new Steuernummer\Validate((string) $elsterSteuernummer->getOutput(), $federalState))->run();
}

function isSteuernummerValid(string $steuernummer, string $federalState): ?bool
{
    return validateSteuernummer($steuernummer, $federalState)->isValid();
}
