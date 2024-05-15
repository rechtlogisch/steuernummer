<?php

/** @noinspection PhpUnusedPrivateMethodInspection */

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer;

use Rechtlogisch\Steuernummer\Exceptions\InvalidBufa;

class Validate extends Common
{
    protected string $elsterSteuernummer;

    protected string $federalState;

    private string $validationProcedure;

    /** @var int[] */
    private array $factors;

    /** @var int[] */
    private array $summands;

    /** @noinspection MagicMethodsValidityInspection @noinspection PhpMissingParentConstructorInspection */
    public function __construct(string $elsterSteuernummer, ?string $federalState = null)
    {
        $this->elsterSteuernummer = $elsterSteuernummer;
        $this->federalState = $federalState ?? $this->determineFederalState();

        $this->guardFederalState();
        $this->guardElsterSteuernummer();
        $this->guardBufa();

        $this->setValidationDetails();
    }

    public function run(): bool
    {
        $validationMethod = $this->validationProcedure.'Procedure';

        return $this->$validationMethod();
    }

    private function setValidationDetails(): void
    {
        $validationProcedure = Constants::FEDERAL_STATES_DETAILS[$this->federalState]['validationProcedure'];
        $this->validationProcedure = $validationProcedure;
        if ($this->federalState === 'BE') {
            $this->factors = $this->determineFactorsForBE();
        } else {
            $this->factors = Constants::FEDERAL_STATES_DETAILS[$this->federalState]['factors'] ?? Constants::FACTORS[$validationProcedure];
        }
        $this->summands = Constants::SUMMANDS[$validationProcedure] ?? [];
    }

    /**
     * @return int[]
     */
    private function determineFactorsForBE(): array
    {
        $beA = Constants::FACTORS['elferBE-A']; // default
        $beB = Constants::FACTORS['elferBE-BandNI'];

        $bufa = substr($this->elsterSteuernummer, 0, Constants::BUFA_LENGTH);

        /** @var string|string[]|null $determinant */
        $determinant = Constants::SUB_PROCEDURES_BE[(int) $bufa] ?? null;

        if ($determinant === null) {
            // already covered by Common::guardBufa()
            throw new InvalidBufa("BUFA {$bufa} does not exists in federal state BE"); // @codeCoverageIgnore
        }

        if ($determinant === 'A') {
            return $beA;
        }

        if ($determinant === 'B') {
            return $beB;
        }

        /** @phpstan-var string[] $determinant */
        if ($this->isProcedureBeBApplicableToDistrict($determinant)) {
            return $beB;
        }

        return $beA;
    }

    /**
     * @param  string[]  $districts
     */
    private function isProcedureBeBApplicableToDistrict(array $districts): bool
    {
        $district = (int) substr($this->elsterSteuernummer, Constants::DISTRICT_INDEX_START, Constants::DISTRICT_LENGTH_DEFAULT);

        foreach ($districts as $range) {
            [$min, $max] = explode('-', $range);

            if (($district >= (int) $min) && ($district <= (int) $max)) {
                return true;
            }
        }

        return false;
    }

    private function elferProcedure(): bool
    {
        $split = str_split($this->elsterSteuernummer);
        $lastDigit = (int) $split[array_key_last($split)];

        $sumOfProducts = 0;

        foreach ($this->factors as $index => $factor) {
            $sumOfProducts += $factor * (int) $split[$index];
        }

        $nextDividableBy11 = $sumOfProducts;

        while ($nextDividableBy11 % 11 !== 0) {
            $nextDividableBy11++;
        }

        $difference = $nextDividableBy11 - $sumOfProducts;

        // not documented
        if ($difference >= 10) {
            return false;
        }

        return $lastDigit === $difference;
    }

    /**
     * Not named separately in ELSTER documentation
     * Different approach explained in text of chapter 6.6
     */
    private function specialElferNWProcedure(): bool
    {
        $split = str_split($this->elsterSteuernummer);
        $lastDigit = (int) $split[array_key_last($split)];

        $sumOfProducts = 0;

        foreach ($this->factors as $index => $factor) {
            $sumOfProducts += $factor * (int) $split[$index];
        }

        $nextDividableBy11 = $sumOfProducts;

        while ($nextDividableBy11 % 11 !== 0) {
            $nextDividableBy11--;
        }

        $difference = $sumOfProducts - $nextDividableBy11;

        return $lastDigit === $difference;
    }

    private function zweierProcedure(): bool
    {
        $split = str_split($this->elsterSteuernummer);
        $lastDigit = (int) $split[array_key_last($split)];

        $sums = [];

        foreach ($this->summands as $index => $summand) {
            $sum = $summand + (int) $split[$index];

            if ($sum > 9) {
                $string = (string) $sum;
                $sum = (int) substr($string, -1);
            }

            $sums[] = $sum;
        }

        $products = [];

        foreach ($sums as $index => $sum) {
            $products[] = $sum * $this->factors[$index];
        }

        $crossfoots = [];
        foreach ($products as $product) {
            $crossfoots[] = $this->crossfoot((string) $product);
        }

        $sumOfSingleDigitCrossfoots = array_sum($crossfoots);

        if ($sumOfSingleDigitCrossfoots % 10 === 0) {
            return $lastDigit === 0;
        }

        $nextDividableBy10 = $sumOfSingleDigitCrossfoots;

        while ($nextDividableBy10 % 10 !== 0) {
            $nextDividableBy10++;
        }

        $difference = $nextDividableBy10 - $sumOfSingleDigitCrossfoots;

        return $lastDigit === $difference;
    }

    /**
     * Named in ELSTER documentation: "Das modifizierte 11er Verfahren (Rheinland-Pfalz)"
     */
    private function specialElferRPProcedure(): bool
    {
        $split = str_split($this->elsterSteuernummer);
        $lastDigit = (int) $split[array_key_last($split)];

        $multiplications = [];

        foreach ($this->factors as $index => $factor) {
            $multiplication = $factor * (int) $split[$index];
            if (strlen((string) $multiplication) > 1) {
                $multiplication = (int) substr((string) $multiplication, -1) + 1;
            }
            $multiplications[] = $multiplication;
        }

        $sumOfMultiplications = array_sum($multiplications);

        $nextDividableBy10 = $sumOfMultiplications;

        while ($nextDividableBy10 % 10 !== 0) {
            $nextDividableBy10++;
        }

        $difference = $nextDividableBy10 - $sumOfMultiplications;

        return $lastDigit === $difference;
    }

    private function crossfoot(string $input): int
    {
        for ($result = $i = 0, $max = strlen($input); $i < $max; $i++) {
            $result += (int) $input[$i];
        }

        if ($result > 9) {
            $result = $this->crossfoot((string) $result);
        }

        return $result;
    }
}
