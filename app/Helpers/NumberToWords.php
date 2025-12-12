<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convertToWords(int $number): string
    {
        $words = [
            0 => 'zéro', 1 => 'un', 2 => 'deux', 3 => 'trois', 4 => 'quatre',
            5 => 'cinq', 6 => 'six', 7 => 'sept', 8 => 'huit', 9 => 'neuf',
            10 => 'dix', 11 => 'onze', 12 => 'douze', 13 => 'treize', 14 => 'quatorze',
            15 => 'quinze', 16 => 'seize', 17 => 'dix-sept', 18 => 'dix-huit', 19 => 'dix-neuf',
            20 => 'vingt', 30 => 'trente', 40 => 'quarante', 50 => 'cinquante',
            60 => 'soixante', 70 => 'soixante-dix', 80 => 'quatre-vingts', 90 => 'quatre-vingt-dix'
        ];

        if ($number <= 20) return $words[$number];

        if ($number < 100) {
            $tens  = intdiv($number, 10) * 10;
            $units = $number % 10;
            if ($tens == 70 || $tens == 90) {
                return $words[$tens - 10] . '-' . $words[$units + 10];
            }
            return $units ? $words[$tens] . '-' . $words[$units] : $words[$tens];
        }

        if ($number < 1000) {
            $hundreds  = intdiv($number, 100);
            $remainder = $number % 100;
            $hundredText = $hundreds > 1 ? $words[$hundreds] . ' cent' : 'cent';
            if ($remainder === 0 && $hundreds > 1) $hundredText .= 's';
            return $hundredText . ($remainder ? ' ' . self::convertToWords($remainder) : '');
        }

        if ($number < 1000000) {
            $thousands = intdiv($number, 1000);
            $remainder = $number % 1000;
            $thousandText = $thousands > 1 ? self::convertToWords($thousands) . ' mille' : 'mille';
            return $thousandText . ($remainder ? ' ' . self::convertToWords($remainder) : '');
        }

        return 'Nombre trop grand';
    }

    public static function convertDecimalToWords($number): string
    {
        if (!is_numeric($number)) return 'zéro';
        $number = round((float)$number, 2);

        $str = (string) $number;
        if (strpos($str, '.') === false) {
            return self::convertToWords((int)$number);
        }

        [$integerPart, $fractionalPart] = explode('.', $str);
        $integerWords    = self::convertToWords((int)$integerPart);
        $fractionalPart  = str_pad($fractionalPart, 2, '0', STR_PAD_RIGHT);
        $fractionalWords = self::convertToWords((int)$fractionalPart);

        return $integerWords . ' virgule ' . $fractionalWords;
    }
}
