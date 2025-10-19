
<?php
// app/Helpers/helpers.php

use function Illuminate\Log\log;

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

function getBankList(){
    $banks = [];

        try {
            $json = file_get_contents(base_path('database/banks.json'));
            $banks = json_decode($json, true);
        } catch (\Exception $e) {
            log('Error reading banks.json: ' . $e->getMessage());
            $banks = [];
        }
    return $banks;

}
