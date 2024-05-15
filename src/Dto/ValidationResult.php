<?php

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer\Dto;

final class ValidationResult
{
    private ?bool $valid = null;

    /** @var string[]|null */
    private ?array $errors = null;

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * @return string[]|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
}
