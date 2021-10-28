<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Iniciador;
use App\Models\TipoEntidad;
use Illuminate\Http\Request;

class IniciadorController extends Controller
{
    public function create()
    {
        $tipo_entidad = TipoEntidad::all();
        return $tipo_entidad;
    }

    public function store(Request $request)
    {
        $iniciador = new Iniciador;
        $iniciador->id_tipo_entidad = $request->tipo_entidad;
        $iniciador->nombre = $request->nombre;
        $iniciador->cuit = $request->cuit;
        $iniciador->area_reparticiones = $request->area_reparticiones;
        $iniciador->prefijo = $request->prefijo;
        $iniciador->save();
        return 'guardado';
    }
}
