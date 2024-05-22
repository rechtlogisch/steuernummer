<?php

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer;

use Rechtlogisch\Steuernummer\Dto\DenormalizationResult;
use Throwable;

class Denormalize extends Common
{
    private DenormalizationResult $result;

    protected string $elsterSteuernummer;

    protected string $federalState;

    public function __construct(string $elsterSteuernummer, ?string $federalState = null)
    {
        $this->result = new DenormalizationResult();
        $this->result->setInput($elsterSteuernummer);
        $this->elsterSteuernummer = $elsterSteuernummer;

        try {
            $this->federalState = $federalState ?? $this->determineFederalState();

            $this->guardFederalState();
            $this->result->setFederalState($this->federalState);
            $this->guardElsterSteuernummer();
            $this->guardBufa();
        } catch (Throwable $exception) {
            $this->result->setValid(false);
            $exceptionType = get_class($exception);
            $this->result->addError($exceptionType, $exception->getMessage());
        }

        parent::__construct();
    }

    public function run(): DenormalizationResult
    {
        if ($this->result->isValid() === false) {
            return $this->result;
        }

        $federalStateDetails = Constants::FEDERAL_STATES_DETAILS[$this->federalState];

        $taxOfficePrefix = $federalStateDetails['taxOfficePrefix'];
        $districtLength = $federalStateDetails['districtLength'] ?? Constants::DISTRICT_LENGTH_DEFAULT;

        $taxOfficeIndexStart = strlen($taxOfficePrefix);
        $taxOfficeSuffix = substr($this->elsterSteuernummer, $taxOfficeIndexStart, Constants::BUFA_LENGTH - $taxOfficeIndexStart);

        $district = substr($this->elsterSteuernummer, Constants::DISTRICT_INDEX_START, $districtLength);

        $districtIndexEnd = Constants::DISTRICT_INDEX_START + $districtLength;
        $uniqueAndChecksum = substr($this->elsterSteuernummer, $districtIndexEnd);

        [$firstSeparator, $secondSeparator] = $federalStateDetails['separators'] ?? Constants::SEPARATORS_DEFAULT;

        $taxNumberPrefix = $federalStateDetails['taxNumberPrefix'] ?? null;

        $this->result->setValid(true);
        $this->result->setOutput(
            $taxNumberPrefix.
            $taxOfficeSuffix.
            $firstSeparator.
            $district.
            $secondSeparator.
            $uniqueAndChecksum
        );

        return $this->result;
    }

    public function returnSteuernummerOnly(): ?string
    {
        return $this->run()->getOutput();
    }

    /**
     * @return array{
     *     steuernummer: ?string,
     *     federalState: ?string
     * }
     */
    public function returnWithFederalState(): array
    {
        $this->run();

        return [
            'steuernummer' => $this->result->getOutput(),
            'federalState' => $this->result->getFederalState(),
        ];
    }
}
