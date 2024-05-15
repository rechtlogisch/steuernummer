<?php

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer;

use Rechtlogisch\Steuernummer\Exceptions\InvalidElsterSteuernummerLength;
use Rechtlogisch\Steuernummer\Exceptions\InvalidSteuernummerLength;

class Normalize extends Common
{
    protected string $steuernummer;

    protected string $federalState;

    /** @noinspection MagicMethodsValidityInspection @noinspection PhpMissingParentConstructorInspection */
    public function __construct(string $steuernummer, string $federalState)
    {
        $this->steuernummer = (string) preg_replace('/\D/', '', $steuernummer); // Consider only digits
        $this->federalState = $federalState;

        $this->guardFederalState();
        $this->guardSteuernummer();
    }

    private function guardSteuernummer(): void
    {
        $expectedLength = in_array($this->federalState, Constants::FEDERAL_STATES_STEUERNUMMER_10_DIGITS)
            ? 10
            : 11;
        $actualLength = strlen($this->steuernummer);

        if ($expectedLength !== $actualLength) {
            throw new InvalidSteuernummerLength("steuernummer for {$this->federalState} must contain exactly {$expectedLength} digits and {$actualLength} digits have been provided");
        }
    }

    public function run(): string
    {
        $tokens = $this->tokenize();
        $compiled = $this->compile($tokens);

        $elsterSteuernummerLength = Constants::ELSTER_STEUERNUMMER_LENGTH;
        if (strlen($compiled) !== $elsterSteuernummerLength) {
            // this shouldn't happen
            throw new InvalidElsterSteuernummerLength("normalization outcome is not {$elsterSteuernummerLength} digits long"); // @codeCoverageIgnore
        }

        return $compiled;
    }

    /**
     * @return array<int|string, string>
     */
    public function tokenize(): array
    {
        $pattern = '//';

        // Formats based on https://www.elster.de/eportal/helpGlobal?themaGlobal=wo%5Fist%5Fmeine%5Fsteuernummer%5Feop#aufbauSteuernummer
        switch ($this->federalState) {
            case 'BE':
            case 'BW':
            case 'HB':
            case 'HH':
            case 'NI':
            case 'RP':
            case 'SH':
                // Format: FF/BBB/UUUUP
                $pattern = '/(?<F>\d{2})(?<B>\d{3})(?<U>\d{4})(?<P>\d{1})/';

                break;
            case 'BB':
            case 'BY':
            case 'MV':
            case 'SL':
            case 'SN':
            case 'ST':
            case 'TH':
                // Format: FFF/BBB/UUUUP
                $pattern = '/(?<F>\d{3})(?<B>\d{3})(?<U>\d{4})(?<P>\d{1})/';

                break;
            case 'HE':
                // Format: 0FF/BBB/UUUUP
                $pattern = '/0(?<F>\d{2})(?<B>\d{3})(?<U>\d{4})(?<P>\d{1})/';

                break;
            case 'NW':
                // Format: FFF/BBBB/UUUP
                $pattern = '/(?<F>\d{3})(?<B>\d{4})(?<U>\d{3})(?<P>\d{1})/';

                break;
        }

        preg_match($pattern, $this->steuernummer, $matches);

        return $matches;
    }

    /**
     * @param  array<int|string>  $tokens
     */
    private function compile(array $tokens): string
    {
        $prefix = Constants::FEDERAL_STATES_DETAILS[$this->federalState]['taxOfficePrefix'];

        // ELSTER-Steuernummerformat based on https://www.elster.de/eportal/helpGlobal?themaGlobal=wo%5Fist%5Fmeine%5Fsteuernummer%5Feop#aufbauSteuernummer
        return $prefix.
            $tokens['F'].
            '0'. // 5th digit is 0 in an ELSTER-Steuernummer and is called format key (German: Formatschlüssel)
            $tokens['B'].
            $tokens['U'].
            $tokens['P'];
    }
}
