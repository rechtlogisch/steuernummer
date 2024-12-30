<?php

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer;

use Rechtlogisch\Steuernummer\Exceptions\ElsterSteuernummerCanContainOnlyDigits;
use Rechtlogisch\Steuernummer\Exceptions\FederalStateCouldNotBeDetermined;
use Rechtlogisch\Steuernummer\Exceptions\InvalidBufa;
use Rechtlogisch\Steuernummer\Exceptions\InvalidDistrict;
use Rechtlogisch\Steuernummer\Exceptions\InvalidElsterSteuernummer;
use Rechtlogisch\Steuernummer\Exceptions\InvalidElsterSteuernummerFormatKey;
use Rechtlogisch\Steuernummer\Exceptions\InvalidElsterSteuernummerLength;
use Rechtlogisch\Steuernummer\Exceptions\InvalidFederalState;

class Common
{
    protected string $elsterSteuernummer;

    protected string $federalState;

    public function __construct(?string $elsterSteuernummer = null, ?string $federalState = null)
    {
        if ($elsterSteuernummer !== null) {
            $this->elsterSteuernummer = $elsterSteuernummer;
        }
        if ($federalState !== null) {
            $this->federalState = $federalState;
        }
    }

    public function guardFederalState(): void
    {
        if (! array_key_exists($this->federalState, Constants::FEDERAL_STATES_DETAILS)) {
            // https://www.iso.org/obp/ui/#iso:code:3166:DE
            throw new InvalidFederalState('federalState must be an ISO 3166-2:DE code (two last characters)');
        }
    }

    public function guardElsterSteuernummer(): void
    {
        if (! is_numeric($this->elsterSteuernummer)) {
            throw new ElsterSteuernummerCanContainOnlyDigits('elsterSteuernummer can contain only digits');
        }

        $expectedElsterSteuernummerLength = Constants::ELSTER_STEUERNUMMER_LENGTH;
        $actualElsterSteuernummerLength = strlen($this->elsterSteuernummer);
        if ($actualElsterSteuernummerLength !== $expectedElsterSteuernummerLength) {
            throw new InvalidElsterSteuernummerLength("elsterSteuernummer must be {$expectedElsterSteuernummerLength} digits long. You provided: {$actualElsterSteuernummerLength} digits.");
        }

        $formatKey = Constants::ELSTER_STEUERNUMMER_FORMAT_KEY;
        $fifthDigit = $this->elsterSteuernummer[4]; // index starts at 0
        if ($fifthDigit !== $formatKey) {
            throw new InvalidElsterSteuernummerFormatKey("elsterSteuernummer must contain {$formatKey} as the formatKey (fifth digit), and it contains {$fifthDigit} instead");
        }

        $districtLength = Constants::FEDERAL_STATES_DETAILS[$this->federalState]['districtLength'] ?? Constants::DISTRICT_LENGTH_DEFAULT;
        $district = substr($this->elsterSteuernummer, Constants::DISTRICT_INDEX_START, $districtLength);
        if (in_array($district, Constants::DISTRICTS_NOT_ALLOWED, true)) {
            throw new InvalidDistrict("elsterSteuernummer contains an invalid district number {$district}");
        }

        // run further checks only for federal states with constrains
        $federalStatesWithConstrainDistrictMin100 = ['BB', 'BY', 'MV', 'SL', 'SN', 'ST', 'TH'];
        $federalStatesWithConstrainMinimalUniqueAndChecksumValue = ['NW'];
        $allFederalStatesWithConstrains = array_merge($federalStatesWithConstrainDistrictMin100, $federalStatesWithConstrainMinimalUniqueAndChecksumValue);
        if (! in_array($this->federalState, $allFederalStatesWithConstrains, true)) {
            return;
        }

        $districtMin = 100;
        if ((int) $district < $districtMin && in_array($this->federalState, $federalStatesWithConstrainDistrictMin100, true)) {
            throw new InvalidDistrict("the district number in the elsterSteuernummer in federal state {$this->federalState} cannot be lower than {$districtMin}, and district {$district} was provided");
        }

        $uniqueAndChecksumLength = Constants::ELSTER_STEUERNUMMER_LENGTH - (Constants::DISTRICT_INDEX_START + Constants::DISTRICT_LENGTH_DEFAULT) - 1;
        $uniqueAndChecksum = substr($this->elsterSteuernummer, -$uniqueAndChecksumLength);
        $unplausibleUniqueAndChecksumValue = 10;
        if ($this->federalState === 'NW' && (int) $uniqueAndChecksum < $unplausibleUniqueAndChecksumValue) {
            throw new InvalidElsterSteuernummer("in {$this->federalState} an elsterSteuernummer ending with four last digits lower than {$unplausibleUniqueAndChecksumValue} is not plausible");
        }

        // unplausible digits chain '99999999' in BY after format key not being checked as not reachable
        // district 999 not allowed in all federal states
    }

    public function guardBufa(?string $input = null): void
    {
        $bufa = (int) substr($input ?? $this->elsterSteuernummer, 0, Constants::BUFA_LENGTH);
        $federalState = $this->federalState ?? $this->determineFederalState();
        $supported = Bufas::SUPPORTED[$federalState] ?? [];
        $test = (getenv('STEUERNUMMER_PRODUCTION') === 'true') ? [] : Bufas::TEST;
        if (! in_array($bufa, array_merge($supported, $test), true)) {
            throw new InvalidBufa("BUFA {$bufa} is not supported in federalState {$federalState}");
        }
    }

    protected function determineFederalState(): string
    {
        $twoFirstDigits = (int) substr($this->elsterSteuernummer, 0, 2);
        $firstDigit = (int) $this->elsterSteuernummer[0];

        $federalState = Constants::TAX_OFFICE_PREFIXES[$twoFirstDigits] ?? Constants::TAX_OFFICE_PREFIXES[$firstDigit] ?? null;

        if ($federalState !== null) {
            return $federalState;
        }

        $expectedBufaLength = Constants::BUFA_LENGTH;
        $bufa = substr($this->elsterSteuernummer, 0, $expectedBufaLength);
        if (($actualBufaLength = strlen($bufa)) < 4) {
            throw new InvalidElsterSteuernummerLength("bufa in elsterSteuernummer must be {$expectedBufaLength} digits long. You provided: {$actualBufaLength} digits.");
        }

        $bufasTest = (getenv('STEUERNUMMER_PRODUCTION') === 'true') ? [] : Bufas::TEST;
        // array_values returns an array with int key index, which causes a PHPStan warning
        $bufasSupported = array_merge($bufasTest, ...array_values(Bufas::SUPPORTED)); // @phpstan-ignore-line
        if (! in_array($bufa, $bufasSupported, true)) {
            throw new InvalidBufa("BUFA {$bufa} is not supported by ERiC");
        }

        // @codeCoverageIgnoreStart
        // can't imagine currently a case this line could be reached
        throw new FederalStateCouldNotBeDetermined("the federal state could not be determined based on the elsterSteuernummer. Please check your inputted elsterSteuernummer: {$this->elsterSteuernummer} or provide the federalState");
        // @codeCoverageIgnoreEnd
    }
}
