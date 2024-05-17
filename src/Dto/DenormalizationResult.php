<?php

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer\Dto;

use Rechtlogisch\Steuernummer\Abstracts\DeNormalizationDto;

final class DenormalizationResult extends DeNormalizationDto
{
    private ?string $federalState = null;

    public function setFederalState(string $federalState): void
    {
        $this->federalState = $federalState;
    }

    public function getFederalState(): ?string
    {
        return $this->federalState;
    }
}
