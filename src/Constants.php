<?php

/** @noinspection PackedHashtableOptimizationInspection */

declare(strict_types=1);

namespace Rechtlogisch\Steuernummer;

class Constants
{
    /** @var string */
    public const ELSTER_STEUERNUMMER_FORMAT_KEY = '0'; // German: "Formatschlüssel"

    /** @var int */
    public const ELSTER_STEUERNUMMER_LENGTH = 13;

    /** @var int */
    public const BUFA_LENGTH = 4; // also called BUFA

    /** @var int */
    public const DISTRICT_INDEX_START = self::BUFA_LENGTH + 1;

    /** @var int */
    public const DISTRICT_LENGTH_DEFAULT = 3;

    /** @var string[] */
    public const SEPARATORS_DEFAULT = ['/', '/'];

    /**
     * @var array<string, array{
     *     name: string,
     *     taxOfficePrefix: string,
     *     validationProcedure: string,
     *     factors?: int[],
     *     separators?: string[],
     *     districtLength?: int,
     *     taxNumberPrefix?: string,
     * }>
     */
    public const FEDERAL_STATES_DETAILS = [
        'BB' => [
            'name' => 'Brandenburg',
            'taxOfficePrefix' => '3',
            'validationProcedure' => 'elfer',
        ],
        'BE' => [
            'name' => 'Berlin',
            'taxOfficePrefix' => '11',
            'validationProcedure' => 'elfer',
            // 'factors' => 'elferBE*',
        ],
        'BW' => [
            'name' => 'Baden-Württemberg',
            'taxOfficePrefix' => '28',
            'validationProcedure' => 'zweier',
            'separators' => ['', '/'],
        ],
        'BY' => [
            'name' => 'Bayern',
            'taxOfficePrefix' => '9',
            'validationProcedure' => 'elfer',
        ],
        'HB' => [
            'name' => 'Bremen',
            'taxOfficePrefix' => '24',
            'validationProcedure' => 'elfer',
            'factors' => self::FACTORS['elferHBandHH'],
            'separators' => [' ', ' '],
        ],
        'HE' => [
            'name' => 'Hessen',
            'taxOfficePrefix' => '26',
            'validationProcedure' => 'zweier',
            'separators' => [' ', ' '],
            'taxNumberPrefix' => '0',
        ],
        'HH' => [
            'name' => 'Hamburg',
            'taxOfficePrefix' => '22',
            'validationProcedure' => 'elfer',
            'factors' => self::FACTORS['elferHBandHH'],
        ],
        'MV' => [
            'name' => 'Mecklenburg-Vorpommern',
            'taxOfficePrefix' => '4',
            'validationProcedure' => 'elfer',
        ],
        'NI' => [
            'name' => 'Niedersachsen',
            'taxOfficePrefix' => '23',
            'validationProcedure' => 'elfer',
            'factors' => self::FACTORS['elferBE-BandNI'],
        ],
        'NW' => [
            'name' => 'Nordrhein-Westfalen',
            'taxOfficePrefix' => '5',
            'validationProcedure' => 'specialElferNW',
            'factors' => self::FACTORS['specialElferNW'],
            'districtLength' => 4,
        ],
        'RP' => [
            'name' => 'Rheinland-Pfalz',
            'taxOfficePrefix' => '27',
            'validationProcedure' => 'specialElferRP',
        ],
        'SH' => [
            'name' => 'Schleswig-Holstein',
            'taxOfficePrefix' => '21',
            'validationProcedure' => 'zweier',
        ],
        'SL' => [
            'name' => 'Saarland',
            'taxOfficePrefix' => '1',
            'validationProcedure' => 'elfer',
        ],
        'SN' => [
            'name' => 'Sachsen',
            'taxOfficePrefix' => '3',
            'validationProcedure' => 'elfer',
        ],
        'ST' => [
            'name' => 'Sachsen-Anhalt',
            'taxOfficePrefix' => '3',
            'validationProcedure' => 'elfer',
        ],
        'TH' => [
            'name' => 'Thüringen',
            'taxOfficePrefix' => '4',
            'validationProcedure' => 'elfer',
        ],
    ];

    /** @var string[] */
    public const FEDERAL_STATES_STEUERNUMMER_10_DIGITS = [
        'BE',
        'BW',
        'HB',
        'HH',
        'NI',
        'RP',
        'SH',
    ];

    /**
     * @var array<string, array{
     *     int,
     * }>
     */
    public const FACTORS = [
        'elfer' => [0, 5, 4, 3, 0, 2, 7, 6, 5, 4, 3, 2],
        'elferBE-A' => [0, 0, 0, 0, 0, 7, 6, 5, 8, 4, 3, 2],
        'elferBE-BandNI' => [0, 0, 2, 9, 0, 8, 7, 6, 5, 4, 3, 2],
        'elferHBandHH' => [0, 0, 4, 3, 0, 2, 7, 6, 5, 4, 3, 2],
        'specialElferNW' => [0, 3, 2, 1, 0, 7, 6, 5, 4, 3, 2, 1],
        'specialElferRP' => [0, 0, 1, 2, 0, 1, 2, 1, 2, 1, 2, 1],
        'zweier' => [0, 0, 512, 256, 0, 128, 64, 32, 16, 8, 4, 2],
    ];

    /**
     * @var array<string, array{
     *     int,
     * }>
     */
    public const SUMMANDS = [
        'zweier' => [0, 0, 9, 8, 0, 7, 6, 5, 4, 3, 2, 1],
    ];

    /**
     * BE has two factors depending on BUFA (and district)
     * cf. subchapter 7.2 in https://download.elster.de/download/schnittstellen/Pruefung_der_Steuer_und_Steueridentifikatsnummer.pdf
     *
     * key is BUFA
     * value is either
     *  string for procedure (A|B) in BE or
     *  array of ranges as strings min-max for districts in BE
     *   if district in range factors B are applied
     *
     * factors
     *  A: elferBE-A
     *  B: elferBE-BandNI
     *
     * @var array<int, string|string[]>
     */
    public const SUB_PROCEDURES_BE = [
        1113 => ['201-693'],
        1114 => ['201-693'],
        1115 => 'B',
        1116 => ['1-29', '201-693', '875-899'],
        1117 => ['201-693'],
        1118 => 'B',
        1119 => ['201-639', '680-684'],
        1120 => ['201-693'],
        1121 => ['201-693'],
        1123 => ['201-693'],
        1124 => ['201-693'],
        1125 => ['201-693'],
        1127 => 'A',
        1129 => 'A',
        1130 => 'A',
        1131 => 'B',
        1132 => 'B',
        1133 => 'B',
        1134 => 'B',
        1135 => 'B',
        1136 => 'B',
        1137 => 'B',
        1138 => 'B',
        // Following BUFAs are designated only for testing purposes
        1194 => 'B',
        1195 => 'B',
        1196 => 'B',
        1197 => 'B',
        1198 => 'B',
    ];

    /** @var array<int, string> */
    public const TAX_OFFICE_PREFIXES = [
        10 => 'SL',
        11 => 'BE',
        21 => 'SH',
        22 => 'HH',
        23 => 'NI',
        24 => 'HB',
        26 => 'HE',
        27 => 'RP',
        28 => 'BW',
        30 => 'BB',
        31 => 'ST',
        32 => 'SN',
        40 => 'MV',
        41 => 'TH',
        5 => 'NW',
        9 => 'BY',
    ];

    /** @var string[] */
    public const DISTRICTS_NOT_ALLOWED = [
        '000',
        '998',
        '999',
        '0000',
        '0998',
        '0999',
    ];

    /**
     * @return array<string, string[]>
     */
    public static function groupFactors(): array
    {
        $result = [];

        foreach (self::FEDERAL_STATES_DETAILS as $federalState => $details) {
            $validationProcedure = $details['validationProcedure'];
            $factors = $details['factors'] ?? self::FACTORS[$validationProcedure];
            // ignore federal states with multiple factors (currently BE)
            if ($federalState === 'BE') {
                continue;
            }
            $implodedFactors = implode('', $factors);
            $result[$implodedFactors][] = $federalState;
        }

        // BE has two factors depending on BUFA (and district)
        $beA = self::FACTORS['elferBE-A'];
        $result[implode('', $beA)][] = 'BE';
        $beB = self::FACTORS['elferBE-BandNI'];
        $result[implode('', $beB)][] = 'BE';

        return $result;
    }

    /**
     * @return array<string, string[]>
     */
    public static function groupValidationProcedures(): array
    {
        $result = [];

        foreach (self::FEDERAL_STATES_DETAILS as $federalState => $details) {
            $validationProcedure = $details['validationProcedure'];
            $result[$validationProcedure][] = $federalState;
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    public static function listValidationProcedures(): array
    {
        $result = [];

        foreach (self::FEDERAL_STATES_DETAILS as $details) {
            $name = $details['name'];
            $result[$name] = $details['validationProcedure'];
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    public static function federalStatesNames(): array
    {
        return array_map(static fn (array $item) => $item['name'], self::FEDERAL_STATES_DETAILS);
    }

    /**
     * @return string[]
     */
    public static function federalStatesCodes(): array
    {
        return array_keys(self::FEDERAL_STATES_DETAILS);
    }

    /**
     * @return array<int, string>
     */
    public static function federalStatesSteuernummer11Digits(): array
    {
        $federalStatesSteuernummer10Digits = self::FEDERAL_STATES_STEUERNUMMER_10_DIGITS;
        $allFederalStatesCodes = self::federalStatesCodes();

        return array_diff($allFederalStatesCodes, $federalStatesSteuernummer10Digits);
    }
}
