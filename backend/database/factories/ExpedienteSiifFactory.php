<?php

namespace Database\Factories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Iniciador;

class ExpedienteSiifFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        //$result= mysql_query( "SELECT * FROM table_name ORDER BY rand()" ) 
    
        //$sql = "SELECT nombre FROM iniciadores ORDER BY rand(1)";
        //$iniciador = DB::select($sql);

        $id = rand(1,47);
        $iniciador = Iniciador::find($id);
        //return $iniciador;

        return [
            'area_actual_id' => $this->faker->numberBetween(1, 25),
            'nro_expediente_ext' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'fojas' => $this->faker->numberBetween(1, 1000),
            'fecha' => $this->faker->date(),
            'descripcion' => $this->faker->sentence(20),
            'nombre' => $iniciador['nombre'],
            'apellido' => $iniciador['apellido'],
            'dni' => $iniciador['dni'],
            'cuit' => str_replace("-" ,"0", $iniciador['cuit']),
            'cuil' => str_replace("-" ,"0", $iniciador['cuil']),
            'telefono' => str_replace("-" ,"0", $iniciador['telefono']),
            'email' => $iniciador['email'],
            'direccion' => $iniciador['direccion'],
            'area_reparticiones' => $iniciador['id_tipo_entidad']
        ]
        
        ;
    }
}
