<?php

namespace App\Models;

use App\Models\Caratula;
use App\Models\Historial;
use App\Models\Iniciador;
use App\Models\EstadoExpediente;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expediente extends Model
{
    use HasFactory;

    public function caratula()
    {
        return $this->hasOne(Caratula::class);
    }

    public function tipoExpediente()
    {
        return $this->hasOne('App\Models\TipoExpediente','id','tipo_expediente');
    }

    public function estadoExpediente()
    {
        return $this->hasOne(EstadoExpediente::class);
    }

    public function prioridadExpediente()
    {
        return $this->hasOne('App\Models\PrioridadExpediente','id','prioridad_id');
        //$this->hasOne(Phone::class, 'foreign_key', 'local_key');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function area()
    {
        //return $this->morphTo(__FUNCTION__, 'area_actual_type', 'area_actual_id');
        return $this->hasOne('App\Models\Area','id','area_actual_id');
    }

    public function historiales()
    {
        return $this->hasMany(Historial::class);
    }

    public function hijos()
    {
        return $this->hasMany(Expediente::class,'expediente_id');
    }

    public function padre()
    {
        return $this->belongsTo(Expediente::class,'expediente_id');
    }

    public function cantidadCuerpos()
    {
        return ceil($this->fojas/200);
    }

    CONST EXTENSIONES_PERMITIDAS = [
        'docx',
        'pdf',
        'txt',
        'jpg',
        'jpeg',
        'xlsx',
        'xls',
    ];

    //Metodo que calcula peso total de archivos
    public static function peso(Request $array)
    {
        $array = $array->allFiles();
        $sum = 0;
        foreach($array as $a)
        {
            $sum = $sum + filesize($a);
        }
        return $sum;
    }

    /*
    * Retorna una colleccion con los datos del expediente
    */
    public function getDatos()
    {
        $array = Collect(["expediente_id" => $this->id,
                          //"nro_expediente" => $this->nroExpediente($this->caratula->iniciador->prefijo, date("d-m",strtotime($this->fecha)),date("Y",strtotime($this->fecha))),
                          "nro_expediente" => $this->nro_expediente,
                          "fecha" =>  date("d-m-Y", strtotime($this->fecha)),
                          "iniciador" => $this->caratula->iniciador->nombre,
                          "cuit" => $this->caratula->iniciador->cuit,
                          "extracto" => $this->caratula->extracto->descripcion,
                          "area_actual" => $this->area->descripcion]);
        return $array;
    }

    public function datosExpediente()
    {
        $array = Collect(['id'=>$this->id,
                          'prioridad'=>$this->prioridadExpediente->descripcion,
                          'nro_expediente'=>$this->nro_expediente,
                          'extracto'=>$this->caratula->extracto->descripcion,
                          'fecha_creacion'=>$this->created_at->format('d-m-Y'),
                          'tramite'=>$this->tipoExpediente->descripcion,
                          'cuerpos'=>$this->cantidadCuerpos(),
                          'caratula'=>$this->caratula->id,
                          'fojas'=>$this->fojas,
                          'area_actual' => $this->area->descripcion,
                          'area_origen'=>$this->historiales->last()->areaOrigen->descripcion,
                          'archivo'=>$this->archivos
            ]);

        return $array;
    }

    public static function index()
    {
        $expedientes = Expediente::where('expediente_id',null)->get();
        $array_expediente = collect([]);
        foreach ($expedientes as $exp)
        {
            $array_expediente->push([
                                'id'=>$exp->id,
                                'prioridad'=>$exp->prioridadExpediente->descripcion,
                                'nro_expediente'=>$exp->nro_expediente,
                                'extracto'=>$exp->caratula->extracto->descripcion,
                                'fecha_creacion'=>$exp->created_at->format('d-m-Y'),
                                'tramite'=>$exp->tipoExpediente->descripcion,
                                'cuerpos'=>$exp->cantidadCuerpos(),
                                'caratula'=>$exp->caratula->id,
                                'fojas'=>$exp->fojas,
                            ]);
        }
        return $array_expediente;
    }

    public static function nroExpediente($año_exp)
    {
        $expedientes =  DB::table('expedientes')
                        ->whereYear('fecha', $año_exp)
                        ->get();
        $cont = $expedientes->count() + 1;
        $nro_aleatorio = str_pad($cont,5,"0",STR_PAD_LEFT);
        $nro_exp = Iniciador::NRO_INICIADOR . '-'. $nro_aleatorio . '/' . $año_exp;
        return $nro_exp;
    }

    public static function listadoExpedientes($user_id, $estado, $bandeja)
    {
        $Expedientes = Expediente::where('expediente_id', null)->get();
        $user = User::findOrFail($user_id);
        $array_expediente = collect([]);

        foreach ($Expedientes as $exp)
        {
            switch ($bandeja)
            {
                case "1":
                case "4":     #BANDEJA DE ENTRADA O #ENVIADOS
                    $area_destino_id = $exp->historiales->last()->areaDestino->id;
                    $area_destino = $exp->historiales->last()->areaDestino->descripcion;
                    $estado_expediente = $exp->historiales->last()->estado;
                    break;
                case "3": #MI EXPEDIENTE
                    $area_destino_id = $exp->area_actual_id;
                    $area_destino = $exp->area->descripcion;
                    $estado_expediente = $exp->estado_expediente_id;
                    break;
            }

            $array_expediente->push([
                'expediente_id'=>$exp->id,
                'prioridad'=>$exp->prioridadExpediente->descripcion,
                'nro_expediente'=>$exp->nro_expediente,
                'extracto'=>$exp->caratula->extracto->descripcion,
                'fecha_creacion'=>$exp->created_at->format('d-m-Y'),
                'tramite'=>$exp->tipoExpediente->descripcion,
                'cant_cuerpos'=>$exp->cantidadCuerpos(),
                'fojas'=>$exp->fojas,
                'iniciador'=>$exp->caratula->iniciador->nombre,
                'cuit_iniciador'=>$exp->caratula->iniciador->cuit,
                'area_origen_id'=>$exp->historiales->last()->areaOrigen->id,
                'area_origen'=>$exp->historiales->last()->areaOrigen->descripcion,
                'area_destino_id'=>$area_destino_id,
                'area_destino'=>$area_destino,
                //'area_actual_id'=>$exp->area_actual_id,
                //'area_actual'=>$exp->area->descripcion,
                'estado'=>$estado_expediente,
                'user_id'=>$exp->historiales->last()->user_id,
                'archivo'=>$exp->archivos,
                'observacion'=>$exp->caratula->observacion,
                'motivo'=>$exp->historiales->sortByDesc('id')->skip(1)->take(1)->values(),
                'observacion_pase'=>$exp->historiales->last()->observacion
            ]);

        }
        /*
        * Filtro los Exp. por bandeja
        */

        #BANDEJA DE ENTRADA
        if ($bandeja == 1) {
            return $array_expediente->where('area_destino_id',$user->area_id)
                                    ->where('estado',$estado)->values();
        }


        #MIS EXPEDIENTE
        if ($bandeja == 3) {
            return $array_expediente->where('area_destino_id',$user->area_id)
                                    ->where('user_id',$user->id)
                                    ->where('estado',$estado)->values();
        }

        #ENVIADOS
        if ($bandeja == 4) {
            return $array_expediente->where('area_origen_id',$user->area_id)
                                    ->where('user_id',$user->id)
                                    ->where('estado',$estado)->values();
        }
        return $array_expediente;
    }

    /*
    * Busca por nro de la tabla pagos
    */
    public static function buscarPor($valor,$busqueda)
    {
        $lista_expedientes = Collect([]);
        switch ($busqueda)
        {
            case "1": //Busca por nro_expediente
                $expediente = Expediente::where('expediente_id',null)->where('nro_expediente', $valor)->get()->first();//Deberia retornar solo un expediente
                if ($expediente != null)
                {
                    $lista_expedientes->push($expediente->getDatos());
                }
                break;

            case "2": //Busca por cuit iniciador
                $iniciador = Iniciador::where('cuit',$valor)->get()->first();

                if ($iniciador != null)
                {
                    //Recorro las caratulas del iniciador para obtener los expedientes
                    foreach ($iniciador->caratulas as $caratula)
                    {
                        $lista_expedientes->push($caratula->expediente->getDatos());
                    }
                }
                break;
            case "3"://Busca por nro_cheque o nro_transaccion
                $pago = Pago::where('nro', $valor)->get()->first();//Deberia retornar solo un expediente
                if ($pago != null)
                {
                    $lista_expedientes->push($pago->expediente->getDatos());
                }
                break;
            case "4"://Busca por id de iniciador
                $iniciador = Iniciador::findOrFail($valor);
                //Recorro las caratulas del iniciador para obtener los expedientes
                foreach ($iniciador->caratulas as $caratula)
                {
                    $lista_expedientes->push($caratula->expediente->getDatos());
                }
                break;
            case "5": //Busca por nro_expediente_ext
                $expedienteExt = Expediente::where('expediente_id',null)->where('nro_expediente_ext', $valor)->get()->values();
                if ($expedienteExt != null)
                {
                    foreach ($expedienteExt as $expediente)
                    {
                        $lista_expedientes->push($expediente->getDatos());
                    }
                }
                break;

                case "6": //Busca por Norma Legal
                    $valor = 'NORMA LEGAL: '.$valor;
                    /*Hice asi porque de esta forma: $lista_expedientes = $lista_expedientes->where('extracto','LIKE',"%$valor%");
                    no me funcionaba la parte del: 'LIKE',"%$valor%"*/
                    $consulta = DB::table('expedientes')->join('caratulas', 'expedientes.id', '=', 'caratulas.expediente_id')
                                            ->join('extractos', 'caratulas.extracto_id', '=', 'extractos.id')
                                            ->select('expedientes.*', 'caratulas.expediente_id', 'extractos.descripcion')
                                            ->where('tipo_expediente', 3)
                                            ->where('descripcion','LIKE',"%$valor%")
                                            ->get();
                    foreach ($consulta as $item) {
                        $expediente = Expediente::FindOrFail($item->id);
                        $lista_expedientes->push($expediente->getDatos());

                    }
                    break;
        }
        return $lista_expedientes;
    }

/*    public function notificacion($user_id)
    {
        /*$lista_areas = Collect([]);
        $area = User::findOrFail($user_id)->area_id;
        $expediente = $this->tipo_expediente->where('id', 3);*/
    /*    $expediente = Expediente::all()->where('tipo_expediente', 3);
        $user = User::all()->where('area_id', 6)->orWhere('area_id', 14);
        $user = DB::table('users')->where('area_id', 6)->orWhere('area_id', 14)->get('id');
        $datos = [$expediente, $user];
        return $datos;
    }*/

    /**
     * Método que retorna toos los expedientes correspondientes a
     * Subsidios y aportes No Reintegrables
     * A: MF
     */
    public static function listadoExpedientesSubsidioAporteNR()
    {
        $expedientes = DB::table('expedientes')
            ->join('prioridad_expedientes', 'expedientes.prioridad_id', '=', 'prioridad_expedientes.id')
            ->join('caratulas', 'expedientes.id', '=', 'caratulas.expediente_id')
            ->join('estado_expedientes', 'expedientes.estado_expediente_id', '=', 'estado_expedientes.id')
            ->join('extractos', 'caratulas.extracto_id', '=', 'extractos.id')
            ->join('tipo_expedientes', 'expedientes.tipo_expediente', '=', 'tipo_expedientes.id')
            ->select('expedientes.nro_expediente as nroExpediente',
                     'expedientes.fecha as fecha_creacion',
                     'prioridad_expedientes.descripcion as prioridad',
                     'extractos.descripcion as extracto',
                     'tipo_expedientes.descripcion as tipoExpediente',
                     'expedientes.fojas as cantFojas',
                     DB::raw('truncate((expedientes.fojas / 200), 0) + 1 as cantCuerpos'))
            ->where('expedientes.tipo_expediente',3)
            ->get();
        return $expedientes;
    }
}
