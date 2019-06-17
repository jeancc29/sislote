<?php

use Illuminate\Database\Seeder;
use App\Entity as e;
use App\Types;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipo = Types::where(['renglon' => 'entidad', 'descripcion' => 'Sistema'])->first();

         e::create([
            'nombre' => 'Sistema',
            'idTipo' => $tipo->id,
            'status' => 3
        ]);
    }
}
