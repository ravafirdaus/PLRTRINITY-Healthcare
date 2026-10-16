<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Normalize Indonesian phone number to 08xx format.
     * Handles: +62xxx, 62xxx, 8xxx, 08xxx
     * Strips spaces, dashes, parentheses.
     */
    public static function normalize(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading +62 or 62
        if (str_starts_with($phone, '62') && strlen($phone) > 10) {
            $phone = '0'.substr($phone, 2);
        }

        // Add leading 0 if starts with 8
        if (str_starts_with($phone, '8')) {
            $phone = '0'.$phone;
        }

        return $phone;
    }
}
