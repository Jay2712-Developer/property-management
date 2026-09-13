<?php

if (!function_exists('formatIndianCurrency')) {
    /**
     * Format a numeric amount into Indian currency notation.
     *
     * Rules:
     *  - >= 1,00,00,000 (1 Crore)  → ₹X.XX Cr
     *  - >= 1,00,000    (1 Lakh)   → ₹X.XX L
     *  - Below 1 Lakh              → ₹X,XX,XXX  (Indian comma grouping)
     *
     * @param  int|float|string|null  $amount
     * @return string
     */
    function formatIndianCurrency(int|float|string|null $amount): string
    {
        $amount = (float) ($amount ?? 0);

        if ($amount >= 1_00_00_000) {
            // Crores
            $crores = $amount / 1_00_00_000;
            return '₹' . number_format($crores, 2) . ' Cr';
        }

        if ($amount >= 1_00_000) {
            // Lakhs
            $lakhs = $amount / 1_00_000;
            return '₹' . number_format($lakhs, 2) . ' L';
        }

        // Below 1 Lakh – use Indian comma grouping (e.g. ₹85,000)
        return '₹' . formatIndianNumber($amount);
    }
}

if (!function_exists('formatIndianNumber')) {
    /**
     * Apply Indian-style comma separation to a number.
     *  e.g. 1234567 → "12,34,567"
     *       85000   → "85,000"
     *
     * @param  int|float  $number
     * @return string
     */
    function formatIndianNumber(int|float $number): string
    {
        $number = (int) $number;

        if ($number < 1000) {
            return (string) $number;
        }

        // Split into last 3 digits and the rest
        $last3   = $number % 1000;
        $rest    = (int) ($number / 1000);

        // Remaining digits go in groups of 2
        $groups  = [];
        while ($rest > 0) {
            $groups[] = $rest % 100;
            $rest      = (int) ($rest / 100);
        }

        $result = str_pad((string) $last3, 3, '0', STR_PAD_LEFT);
        foreach ($groups as $group) {
            $result = str_pad((string) $group, 2, '0', STR_PAD_LEFT) . ',' . $result;
        }

        // Trim any leading zeros from the very first segment
        return ltrim($result, '0') ?: '0';
    }
}
