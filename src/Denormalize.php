<?php

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer;

class Denormalize extends Common
{
    protected string $elsterSteuernummer;

    protected string $federalState;

    public function __construct(string $elsterSteuernummer, ?string $federalState = null)
    {
        $this->elsterSteuernummer = $elsterSteuernummer;
        $this->federalState = $federalState ?? $this->determineFederalState();

        $this->guardFederalState();
        $this->guardElsterSteuernummer();

        parent::__construct();
    }

    private function run(): string
    {
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

        return $taxNumberPrefix.
            $taxOfficeSuffix.
            $firstSeparator.
            $district.
            $secondSeparator.
            $uniqueAndChecksum;
    }

    public function returnSteuernummerOnly(): string
    {
        return $this->run();
    }

    /**
     * @return array{
     *     steuernummer: string,
     *     federalState: string
     * }
     */
    public function returnWithFederalState(): array
    {
        return [
            'steuernummer' => $this->run(),
            'federalState' => $this->federalState,
        ];
    }
}
