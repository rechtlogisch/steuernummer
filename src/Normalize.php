<?php

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer;

use Rechtlogisch\Steuernummer\Dto\NormalizationResult;
use Rechtlogisch\Steuernummer\Exceptions\InvalidElsterSteuernummerLength;
use Rechtlogisch\Steuernummer\Exceptions\InvalidSteuernummerLength;
use Throwable;

class Normalize extends Common
{
    private NormalizationResult $result;

    protected string $steuernummer;

    protected string $federalState;

    public function __construct(string $steuernummer, string $federalState)
    {
        $this->result = new NormalizationResult();
        $this->result->setInput($steuernummer);
        $this->steuernummer = (string) preg_replace('/\D/', '', $steuernummer); // Consider only digits
        $this->federalState = $federalState;

        try {
            $this->guardFederalState();
            $this->guardSteuernummer();
        } catch (Throwable $exception) {
            $this->result->setValid(false);
            $exceptionType = get_class($exception);
            $this->result->addError($exceptionType, $exception->getMessage());
        }

        parent::__construct();
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

    public function run(): NormalizationResult
    {
        if ($this->result->isValid() === false) {
            return $this->result;
        }

        $tokens = $this->tokenize();
        $compiled = $this->compile($tokens);

        $elsterSteuernummerLength = Constants::ELSTER_STEUERNUMMER_LENGTH;
        // this shouldn't happen
        // @codeCoverageIgnoreStart
        if (strlen($compiled) !== $elsterSteuernummerLength) {
            $this->result->setValid(false);
            $exceptionType = gettype(InvalidElsterSteuernummerLength::class);
            $this->result->addError($exceptionType, "normalization outcome is not {$elsterSteuernummerLength} digits long");
        }
        // @codeCoverageIgnoreEnd

        $this->result->setValid(true);
        $this->result->setOutput($compiled);

        return $this->result;
    }

    public function returnElsterSteuernummerOnly(): ?string
    {
        return $this->run()->getOutput();
    }

    /**
     * @return array<int|string, string>
     */
    private function tokenize(): array
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
