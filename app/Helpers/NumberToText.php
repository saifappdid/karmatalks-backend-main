<?php

namespace App\Helpers;

class NumberToText
{
    protected static $ones = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
    ];

    protected static $tens = [
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety',
    ];

    /**
     * Convert an amount into words using the Indian numbering system
     * (...Crore, Lakh, Thousand, Hundred).
     *
     * @param float $number
     * @param string $unit Major unit label, e.g. "Rupees" or "US Dollars"
     * @param string $subUnit Minor unit label, e.g. "Paise" or "Cents"
     * @return string
     */
    public static function convertNumberToText($number, $unit = 'Rupees', $subUnit = 'Paise')
    {
        $number = (float) $number;
        $negative = $number < 0;
        $number = round(abs($number), 2);

        $rupees = (int) floor($number);
        $paise = (int) round(($number - $rupees) * 100);

        $words = trim(self::convertWholeNumber($rupees) . ' ' . $unit);

        if ($paise > 0) {
            $words .= ' and ' . trim(self::convertWholeNumber($paise) . ' ' . $subUnit);
        }

        $words .= ' Only';

        return ($negative ? 'Minus ' : '') . $words;
    }

    protected static function convertWholeNumber($number)
    {
        if ($number == 0) {
            return 'Zero';
        }

        $crore = (int) ($number / 10000000);
        $number %= 10000000;
        $lakh = (int) ($number / 100000);
        $number %= 100000;
        $thousand = (int) ($number / 1000);
        $number %= 1000;
        $hundred = (int) ($number / 100);
        $remainder = $number % 100;

        $parts = [];
        if ($crore) {
            $parts[] = self::convertTwoDigits($crore) . ' Crore';
        }
        if ($lakh) {
            $parts[] = self::convertTwoDigits($lakh) . ' Lakh';
        }
        if ($thousand) {
            $parts[] = self::convertTwoDigits($thousand) . ' Thousand';
        }
        if ($hundred) {
            $parts[] = self::$ones[$hundred] . ' Hundred';
        }
        if ($remainder) {
            $parts[] = self::convertTwoDigits($remainder);
        }

        return implode(' ', $parts);
    }

    protected static function convertTwoDigits($number)
    {
        if ($number < 20) {
            return self::$ones[$number];
        }
        $tens = (int) ($number / 10);
        $ones = $number % 10;
        return trim(self::$tens[$tens] . ' ' . self::$ones[$ones]);
    }
}
