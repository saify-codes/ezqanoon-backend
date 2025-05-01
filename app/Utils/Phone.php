<?php

namespace App\Utils;

use Exception;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class Phone
{

    private static $countryCodes = [
        '92' => 'Pakistan',
        '1'  => 'USA',
        '44' => 'UK',
        // Add more country codes as needed
    ];

    /**
     * Convert a phone number to an international format.
     *
     * This method cleans the phone number by removing non-digit characters,
     * strips off a leading zero (if present), and ensures the number starts with
     * the provided country code. If the cleaned number already starts with the country code,
     * it is removed before re-appending it.
     *
     * @param string|null $phone       The phone number to convert.
     * @param string|null $countryCode The country code to prepend, defaults to '92'.
     *
     * @return string|null The phone number in international format, or null if input is empty.
     */
    public static function convertToInternationalFormat(?string $phone, ?string $countryCode = '92'): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-digit characters
        $cleanedPhone = preg_replace('/\D+/', '', $phone);

        // If it starts with 0, remove it before adding country code
        if (strpos($cleanedPhone, '0') === 0) {
            $cleanedPhone = substr($cleanedPhone, 1);
        }

        // If it already starts with the country code, remove it
        if (strpos($cleanedPhone, $countryCode) === 0) {
            $cleanedPhone = substr($cleanedPhone, strlen($countryCode));
        }

        return "+" . $countryCode . $cleanedPhone;
    }

    /**
     * Convert an international phone number to a local format.
     *
     * This method expects an international number formatted as "+{countryCode}{number}".
     * It removes the country code and adds a leading zero.
     *
     * @param string|null $phone       The international phone number.
     * @param string|null $countryCode The country code to remove, defaults to '92'.
     *
     * @return string|null The phone number in local format, or null if input is empty.
     */

    public static function convertToLocalFormat(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-digit characters except the plus sign at the beginning
        $cleanedPhone = preg_replace('/(?!^\+)\D+/', '', $phone);

        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Parse the phone number and attempt to detect the region
            $phoneNumber = $phoneUtil->parse($cleanedPhone, null);

            // Check if the phone number is valid
            if ($phoneUtil->isValidNumber($phoneNumber)) {

                $countryCode = $phoneNumber->getCountryCode();
                
                switch ($countryCode) {
                    case 1:  
                        $areaCode       = substr($cleanedPhone, 2, 3); // After '+1'
                        $localNumber    = substr($cleanedPhone, 5); // After area code
                        return '(' . $areaCode . ') ' . substr($localNumber, 0, 3) . '-' . substr($localNumber, 3);
                        
                    case 92:
                        return '0' . substr($cleanedPhone, 3);
                    
                    default:
                        return $cleanedPhone;
                }

            }

        }catch(Exception $e){}
        
        return null;

    }

    /**
     * Validate if the phone number is in E.164 format.
     *
     * E.164 standard format: +{countryCode}{number}
     *
     * @param string|null $phone The phone number to validate.
     *
     * @return bool True if the phone number is valid in E.164 format, false otherwise.
     */
    public static function isValid(?string $phone, $countryCode): bool
    {
        if (!$phone) {
            return false;
        }


        // Remove all non-digit characters
        $cleanedPhone = preg_replace('/\D+/', '', $phone);

        // Check if the number starts with '+' followed by the country code
        if (strpos($cleanedPhone, $countryCode) !== 0) {
            return false;
        }

        return match ($countryCode) {
            '92' => preg_match('/^92[0-9]{10}$/', $cleanedPhone) === 1,
            default => false
        };
    }

    public static function isValidV2(?string $phone): bool
    {
        if (!$phone) {
            return false;
        }

        // Remove all non-digit characters except the plus sign at the beginning
        $cleanedPhone = preg_replace('/\D+/', '', $phone);


        // Extract country code by checking first digits
        $countryCode = null;
        foreach (array_keys(self::$countryCodes) as $code) {
            if (str_starts_with($cleanedPhone, $code)) {
                $countryCode = $code;
                break;
            }
        }

        if (!$countryCode) {
            return false;
        }



        return match ($countryCode) {
            '92' => preg_match('/^92[0-9]{10}$/', $cleanedPhone) === 1,  // Pakistan: 92XXXXXXXXXX (12 digits)
            '44' => preg_match('/^44[0-9]{10}$/', $cleanedPhone) === 1,  // UK: 44XXXXXXXXXX (12 digits)
            '1'  => preg_match('/^1[0-9]{10}$/', $cleanedPhone) === 1,   // US: 1XXXXXXXXXX (11 digits)
            default => false
        };
    }

    public static function convertToE164Format($phone, $countryCode)
    {
        if (!$phone) {
            throw new \InvalidArgumentException('Phone number cannot be null or empty.');
        }

        $cleanedPhone = preg_replace('/\D+/', '', $phone);

        // If it starts with 0, remove it
        if (strpos($cleanedPhone, '0') === 0) {
            $cleanedPhone = substr($cleanedPhone, 1);
        }

        // If it already starts with the country code, remove it
        if (strpos($cleanedPhone, $countryCode) === 0) {
            $cleanedPhone = substr($cleanedPhone, strlen($countryCode));
        }

        return "+" . $countryCode . $cleanedPhone;
    }
}
