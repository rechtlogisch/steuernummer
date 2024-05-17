<?php

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer\Abstracts;

abstract class ResultDto
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
     * @return array<string, string>|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getFirstErrorKey(): ?string
    {
        return (string) array_key_first($this->errors ?? []);
    }

    public function getFirstError(): ?array
    {
        $errors = $this->getErrors() ?? [];
        $firstKey = array_key_first($errors);

        if ($firstKey === null) {
            return null;
        }

        return [$firstKey => $this->getErrors()[$firstKey]];
    }

    public function addError(string $type, string $error): void
    {
        $this->errors[$type] = $error;
    }
}
