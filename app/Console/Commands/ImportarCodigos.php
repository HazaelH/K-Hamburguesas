<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportarCodigos extends Command
{
    // El nombre para ejecutarlo en la terminal
    protected $signature = 'importar:codigos';

    // Descripción del comando
    protected $description = 'Importa los códigos postales desde un archivo XML en storage/app';

    public function handle()
    {
        $nombreArchivo = 'codigos_postales.xml';
        $rutaCompleta = storage_path('app/' . $nombreArchivo);

        $this->info("Buscando archivo en: " . $rutaCompleta);

        if (!file_exists($rutaCompleta)) {
            $this->error("❌ El archivo no existe.");
            return;
        }

        $this->info('✅ Archivo encontrado. Limpiando y procesando XML...');

        // 1. LEER COMO TEXTO Y LIMPIAR EL ERROR DEL NAMESPACE
        // Esto es lo que arregla el error "URI is not absolute"
        $contenido = file_get_contents($rutaCompleta);
        $contenido = str_replace('xmlns="NewDataSet"', '', $contenido); 

        // 2. CONVERTIR TEXTO A XML
        try {
            // Usamos libxml_use_internal_errors para que ignore advertencias menores
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($contenido);
            
            if ($xml === false) {
                $errores = "";
                foreach(libxml_get_errors() as $error) {
                    $errores .= $error->message . ", ";
                }
                $this->error("Error al interpretar XML: " . $errores);
                return;
            }
        } catch (\Exception $e) {
            $this->error("Excepción crítica: " . $e->getMessage());
            return;
        }

        $this->info('Iniciando importación a la Base de Datos...');

        // NOTA IMPORTANTE:
        // Los XML de Correos de México suelen tener la estructura:
        // <NewDataSet>
        //    <table> ... datos ... </table>
        //    <table> ... datos ... </table>
        // </NewDataSet>
        // Por eso accedemos a $xml->table
        
        $total = count($xml->table);
        $this->info("Se encontraron {$total} códigos postales.");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $dataBatch = [];
        
        // Limpiamos la tabla
        DB::table('zip_codes')->truncate();

        foreach ($xml->table as $row) {
            $dataBatch[] = [
                'codigo_postal' => (string)$row->d_codigo,
                'colonia'       => (string)$row->d_asenta,
                'municipio'     => (string)$row->D_mnpio,
                'estado'        => (string)$row->d_estado,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            // Insertamos en lotes de 500 para velocidad
            if (count($dataBatch) >= 500) {
                DB::table('zip_codes')->insert($dataBatch);
                $dataBatch = [];
            }

            $bar->advance();
        }

        // Insertar los últimos registros
        if (count($dataBatch) > 0) {
            DB::table('zip_codes')->insert($dataBatch);
        }

        $bar->finish();
        $this->newLine();
        $this->info('¡Importación completada con éxito!');
    }
}