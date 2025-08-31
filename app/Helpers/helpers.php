
<?php
// app/Helpers/helpers.php

if (!function_exists('format_currency')) {
    /**
     * Format a number to Nigerian Naira with proper formatting.
     *
     * @param float|int $amount
     * @param string $currencySymbol
     * @return string
     */
    function format_currency($amount, $currencySymbol = '₦') {
        return $currencySymbol . number_format($amount, 0, '.', ',');
    }
}
