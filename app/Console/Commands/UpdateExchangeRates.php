<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class UpdateExchangeRates extends Command
{
    // El nombre con el que ejecutaremos el comando
    protected $signature = 'currency:update';
    protected $description = 'Actualiza el tipo de cambio de MXN a USD/BRL usando una API gratuita';

    public function handle()
    {
        $this->info('Conectando a la API financiera global...');

        // Usamos ExchangeRate-API (Es gratuita y no requiere registro para uso básico)
        // Pedimos los valores base tomando el Peso Mexicano (MXN) como moneda origen
        $response = Http::get('https://open.er-api.com/v6/latest/MXN');

        if ($response->successful()) {
            $data = $response->json();
            
            // La API nos dice cuánto vale 1 MXN en USD. 
            // Para saber cuántos pesos cuesta 1 Dólar, hacemos la inversa matemática (1 / valor)
            $precioDolar = 1 / $data['rates']['USD'];
            $precioReal = 1 / $data['rates']['BRL']; // Para el futuro portugués

            // Guardamos el valor exacto en la Memoria Caché de Laravel por 24 horas
            // Así tu página no se vuelve lenta consultando la API con cada cliente
            Cache::put('exchange_rate_usd', $precioDolar, now()->addDay());
            Cache::put('exchange_rate_brl', $precioReal, now()->addDay());

            $this->info(sprintf("¡Éxito! 1 USD = $%.2f MXN", $precioDolar));
        } else {
            $this->error('No se pudo conectar con la API de divisas.');
        }
    }
}