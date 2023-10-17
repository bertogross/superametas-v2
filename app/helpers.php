<?php

use App\Models\UserMeta;

// Retrieve a user's meta value based on the given key.
if (!function_exists('getUserMeta')) {
    /**
     * @param int    $userId The ID of the user.
     * @param string $key    The meta key to retrieve.
     * @return mixed The meta value or null if not found.
     */
    function getUserMeta($userId, $key)
    {
        return UserMeta::getUserMeta($userId, $key);
    }
}

// Format a phone number to the pattern (XX) X XXXX-XXXX.
if (!function_exists('formatPhoneNumber')) {
    /**
     * @param string $phoneNumber The phone number to be formatted.
     * @return string The formatted phone number or an empty string if the input is empty.
     */
    function formatPhoneNumber($phoneNumber) {
        // Remove all non-numeric characters from the phone number.
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // Apply the desired formatting pattern to the phone number.
        return !empty($phoneNumber) ? preg_replace('/(\d{2})(\d{1})(\d{4})(\d{4})/', '($1) $2 $3-$4', $phoneNumber) : '';
    }
}
