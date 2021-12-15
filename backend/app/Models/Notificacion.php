<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;
    protected $table = "notificaciones";

    public function expediente()
    {
        return $this->hasOne('App\Models\Expediente', 'id', 'expediente_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function index()
    {
        $notificaciones = Notificacion::all()->where('estado', 1);
        $array_notificacion = collect([]);
        foreach ($notificaciones as $notificacion)
        {
            $array_notificacion->push([
                                'id'                => $notificacion->id,
                                'nro_expediente'    => $notificacion->expediente->nro_expediente,
                                'user_creacion'     => $notificacion->expediente->historiales->first()->user->persona->nombre ." " . $notificacion->expediente->historiales->first()->user->persona->apellido,
                                'area'              => $notificacion->user->area->descripcion,
                                'fecha'             => $notificacion->created_at->format('d-m-Y'),
                                'extracto'          => $notificacion->expediente->caratula->extracto->descripcion,
                                'estado'            => $notificacion->estado,
                            ]);
        }
        return $array_notificacion;
        //Expediente::find(3)->historiales->first()->user->persona->nombre
    }

    public function getDatos()
    {
        $array_notificacion = collect([]);
        $array_notificacion->push([
                                'id'                => $this->id,
                                'nro_expediente'    => $this->expediente->nro_expediente,
                                'user_id'           => $this->user->id,
                                'nombre'            => $this->user->persona->nombre ." " . $this->user->persona->apellido,
                                'area'              => $this->user->area->descripcion,
                                'fecha'             => $this->created_at->format('d-m-Y'),
                                'estado'            => $this->estado,
                            ]);
        return $array_notificacion;
    }
}
