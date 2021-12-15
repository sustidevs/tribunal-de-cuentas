<?php

namespace App\Http\Controllers\api;

use Carbon\Carbon;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::index();
        return response()->json($notificaciones, 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        /*$notificacion = new Notificacion;
        $notificacion->expediente_id = $request->expediente_id;
        $notificacion->user_id = $request->user_id;
        $notificacion->estado = "1"; //Pendiente
        $notificacion->save();
        return response()->json($notificacion, 200);*/
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        $notificacion = Notificacion::findOrFail($request->id);
        $notificacion->user_id = $request->user_id;
        $notificacion->fecha = Carbon::now();
        $notificacion->estado = "2"; //Aceptado
        $notificacion->save();
        return response()->json($notificacion->getDatos(), 200);
    }

    public function destroy($id)
    {
        //
    }
}
