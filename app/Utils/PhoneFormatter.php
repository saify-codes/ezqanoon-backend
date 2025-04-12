<?php

namespace App\Utils;

class PhoneFormatter
{
    // Country codes mapping for detecting the country code.
    // This is a simple example, and you can extend it with more country codes.
    private static $countryCodes = [
        '1'  => 'US',
        '92' => 'PK',
        '44' => 'GB',
        '61' => 'AU',
        // Add more country codes as needed.
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
    public static function convertToLocalFormat(?string $phone, ?string $countryCode = '92'): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-digit characters except the plus sign at the beginning
        $cleanedPhone = preg_replace('/(?!^\+)\D+/', '', $phone);

        // Check if the number starts with '+' followed by the country code
        if (strpos($cleanedPhone, '+' . $countryCode) === 0) {
            // Remove the country code and add a leading zero
            $localNumber = '0' . substr($cleanedPhone, strlen($countryCode) + 1);
            return $localNumber;
        }

        // If the country code is not found, assume it's already local
        return $cleanedPhone;
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
    public static function validateE164(?string $phone): bool
    {
        if (!$phone) {
            return false;
        }

        // Check if the phone number starts with a plus sign and is followed by only digits
        return preg_match('/^\+(\d{1,15})$/', $phone) === 1;
    }

    /**
     * Convert a local phone number to E.164 standard format, guessing the country code.
     *
     * This method converts a local phone number (e.g., '03122030440') to the E.164 international format,
     * by detecting the country code and appending it at the start of the cleaned number.
     *
     * @param string|null $phone The local phone number to convert.
     *
     * @return string|null The phone number in E.164 format, or null if input is empty or invalid.
     */
    public static function convertToE164FromLocal(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-digit characters
        $cleanedPhone = preg_replace('/\D+/', '', $phone);

        // Try to match the country code from the beginning of the cleaned phone number
        foreach (self::$countryCodes as $code => $country) {
            if (strpos($cleanedPhone, $code) === 0) {
                // Remove the country code and append it to the E.164 formatted number
                $cleanedPhoneWithoutCode = substr($cleanedPhone, strlen($code));
                return "+" . $code . $cleanedPhoneWithoutCode;
            }
        }

        // If no country code is found, return null or handle as invalid.
        return null;
    }
}
