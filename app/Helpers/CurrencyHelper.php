<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount)
    {
        $locale = app()->getLocale();

        if ($locale === 'en') {
            // Buscamos el valor de la API. Si por algo la API falló, usamos 20.00 como salvavidas (fallback)
            $exchangeRate = Cache::get('exchange_rate_usd', 20.00); 
            $converted = $amount / $exchangeRate;
            return '$' . number_format($converted, 2) . ' USD';
        }
        
        elseif ($locale === 'pt') {
            $exchangeRate = Cache::get('exchange_rate_brl', 3.50);
            $converted = $amount / $exchangeRate;
            return 'R$ ' . number_format($converted, 2);
        }

        return '$' . number_format($amount, 2) . ' MXN';
    }
}

if (!function_exists('convertCurrencyValue')) {
    function convertCurrencyValue($amount)
    {
        $locale = app()->getLocale();
        if ($locale === 'en') return round($amount / Cache::get('exchange_rate_usd', 20.00), 2);
        if ($locale === 'pt') return round($amount / Cache::get('exchange_rate_brl', 3.50), 2);
        return round($amount, 2);
    }
}

if (!function_exists('convertToBaseCurrency')) {
    function convertToBaseCurrency($amount)
    {
        $locale = app()->getLocale();
        if ($locale === 'en') return $amount * Cache::get('exchange_rate_usd', 20.00);
        if ($locale === 'pt') return $amount * Cache::get('exchange_rate_brl', 3.50);
        return $amount;
    }
}