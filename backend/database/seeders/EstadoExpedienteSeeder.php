<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoExpediente;

class EstadoExpedienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            'Pendiente',
            '',
            'Aceptado',
            'Enviado',
            'Recuperado',
            'Expedientes SIIF'
        ];
        foreach($estados as $pri){
            $p = new EstadoExpediente();
            $p->descripcion = $pri;
            $p->save();
        }
    }
}
