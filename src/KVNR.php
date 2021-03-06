<?php

/**
 * @author    Davide Danna
 * @copyright 2020 GAIA AG, Hamburg
 * @package   KVNR-Validator
 *
 * Created using PhpStorm at 07.09.20 10:36
 */

namespace GaiaGroup;

/**
 *  KVNR utility tools class
 */
class KVNR
{
    /**
     * Checks KVNR string format and its check digit
     *
     * @param string $kvnr KVNR string to be validated
     *
     * @return bool true / false
     */
    public static function validate(string $kvnr): bool
    {
        // checks KVNR format using regex
        if (preg_match("/^[A-Z][0-9]{9}$/", $kvnr)) {
            // computes check digit
            $checkDigit = self::computeCheckDigit(substr($kvnr, 0, strlen($kvnr) - 1));

            // compares computed check digit with last digit in provided KVNR
            return $kvnr[9] == $checkDigit;
        }

        return false;
    }

    /**
     * returns the check digit for a given KVNR code.
     * If provided KVNR is not in a valid format it returns -1
     *
     * @param string $kvnr KVNR string
     *
     * @return int a single digit integer, if provided KVNr code is not in a valid format this method returns -1
     */
    private static function computeCheckDigit(string $kvnr)
    {
        // converts first character of KVNR to integer using ASCII
        $digitChar = ord($kvnr[0]) - 64;

        // checks if conversion gave expected values (A->1 ... Z->26)
        if ($digitChar <= 26 && $digitChar >= 1) {
            $kvnrDigits = [];

            // adds 0 left-padding for values less than 10
            $digitChar = str_pad(strval($digitChar), 2, "0", STR_PAD_LEFT);

            // sets first 2 element of digits array
            $kvnrDigits[] = intval($digitChar[0]);
            $kvnrDigits[] = intval($digitChar[1]);

            // sets last 8 element of digits array
            for ($i = 2; $i < 10; $i++) {
                $kvnrDigits[$i] = intval($kvnr[$i - 1]);
            }

            $kvnrDigitsWeighted = [];
            for ($i = 0; $i < 10; $i++) {
                // sets weight array: (1, 2, 1, 2, ...)
                $weight = ($i % 2 == 0) ? 1 : 2;

                // multiplies digit array with weight array
                $kvnrDigitWeighted = $kvnrDigits[$i] * $weight;

                // if resulting number is >= 10 then it's digit are summed
                $kvnrDigitsWeighted[$i] = array_sum(str_split($kvnrDigitWeighted));
            }

            // computes check digit by summing each item in array and applying module-10
            return array_sum($kvnrDigitsWeighted) % 10;
        }

        return -1;
    }
}