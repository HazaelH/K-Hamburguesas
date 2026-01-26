namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZipCodeSeeder extends Seeder
{
    public function run()
    {
        // Ejemplos de Toluca y alrededores
        $data = [
            ['codigo_postal' => '50000', 'colonia' => 'Centro', 'municipio' => 'Toluca', 'estado' => 'Estado de México'],
            ['codigo_postal' => '50120', 'colonia' => 'La Maquinita', 'municipio' => 'Toluca', 'estado' => 'Estado de México'],
            ['codigo_postal' => '52140', 'colonia' => 'La Providencia', 'municipio' => 'Metepec', 'estado' => 'Estado de México'],
            ['codigo_postal' => '52140', 'colonia' => 'Casa Blanca', 'municipio' => 'Metepec', 'estado' => 'Estado de México'], // Mismo CP, diferente colonia
        ];

        DB::table('zip_codes')->insert($data);
    }
}