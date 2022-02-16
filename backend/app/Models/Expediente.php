<?php

namespace App\Models;

use App\Models\Cedula;
use App\Models\Caratula;
use App\Models\Historial;
use App\Models\Iniciador;
use Illuminate\Http\Request;
use App\Models\EstadoExpediente;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
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

    public function cedulas()
    {
        return $this->hasMany(Cedula::class);
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

    public static function detalle_cedulas($expediente_id)
    {
        $expediente = Expediente::find($expediente_id);
        $cedulas = Cedula::all()->where('expediente_id', $expediente_id);
        $array = collect([]);
        foreach ($cedulas as $cedula)
        {
            $array->push([
                        'cedula_id'         => $cedula->id,
                        'user'              => $cedula->user->persona->nombre ." " . $cedula->user->persona->apellido,
                        'nro_expediente'    => $expediente->nro_expediente,
                        'extracto'          => $expediente->caratula->extracto->descripcion,
                        'nro_cedula'        => $cedula->descripcion,
                        'cantidad'          => $expediente->cedulas()->count()
            ]);
        }
        return $array;        
    }

    /*
    * Retorna una colleccion con los datos del expediente
    */
    public function getDatos()
    {
        $array = Collect(["expediente_id"   => $this->id,
                          //"nro_expediente" => $this->nroExpediente($this->caratula->iniciador->prefijo, date("d-m",strtotime($this->fecha)),date("Y",strtotime($this->fecha))),
                          "nro_expediente"  => $this->nro_expediente,
                          "fecha"           => date("d-m-Y", strtotime($this->fecha)),
                          "iniciador"       => $this->caratula->iniciador->nombre,
                          "cuit"            => $this->caratula->iniciador->cuit,
                          "extracto"        => $this->caratula->extracto->descripcion,
                          "area_actual"     => $this->area->descripcion,
                          "cantidad_cedulas"=> $this->cedulas->count()
                        ]);
        return $array;
    }

    public function datosExpediente_old()
    {
        $array = Collect(['id'                  => $this->id,
                          'prioridad'           => $this->prioridadExpediente->descripcion,
                          'nro_expediente'      => $this->nro_expediente,
                          'extracto'            => $this->caratula->extracto->descripcion,
                          'fecha_creacion'      => $this->created_at->format('d-m-Y'),
                          'tramite'             => $this->tipoExpediente->descripcion,
                          'cuerpos'             => $this->cantidadCuerpos(),
                          'caratula'            => $this->caratula->id,
                          'fojas'               => $this->fojas,
                          'area_actual'         => $this->area->descripcion,
                          'area_origen'         => $this->historiales->last()->areaOrigen->descripcion,
                          'archivo'             => $this->archivos,
                          'cantidad_cedulas'    => $this->cedulas->count()
            ]);

        return $array;
    }

    /**
     * Método que devuelve todos los expedientes
     * Autor: Mariano Flores
     */
    public static function datosExpediente()
    {
        $area_origen = DB::table('historiales')
                        ->select('expediente_id', DB::raw('MAX(fecha)'), 'areas.descripcion')
                        ->join('areas', 'areas.id', '=', 'historiales.area_origen_id')
                        ->groupBy('historiales.expediente_id', 'areas.descripcion');

        $query = DB::table('expedientes')
                        ->select('expedientes.id',
                                 'prioridad_expedientes.descripcion as prioridad',
                                 'expedientes.nro_expediente',
                                 'extractos.descripcion as extracto',
                                 'expedientes.fecha as fecha_creacion',
                                 'tipo_expedientes.descripcion as tramite',
                                 DB::raw('ceil(expedientes.fojas / 200) as cuerpos'),
                                 'caratulas.id as caratula',
                                 'expedientes.fojas',
                                 'areas.descripcion as area_actual',
                                 'areaOrigen.descripcion as area_origen',
                                 'expedientes.archivos as archivo'
                                 )
                        ->where('expedientes.expediente_id', '=', null)
                        ->joinSub($area_origen, 'areaOrigen', function($join)
                        {
                            $join->on('expedientes.id', '=', 'areaOrigen.expediente_id');
                        })     
                        ->join('prioridad_expedientes', 'prioridad_expedientes.id', '=', 'expedientes.prioridad_id')
                        ->join('caratulas', 'expedientes.id', '=', 'caratulas.expediente_id')
                        ->join('extractos', 'caratulas.extracto_id', '=', 'extractos.id')
                        ->join('tipo_expedientes', 'expedientes.tipo_expediente', '=', 'tipo_expedientes.id')
                        ->join('areas', 'areas.id', '=', 'expedientes.area_actual_id')
                        ->orderBy('expedientes.id')
                        ->get();
        return $query;
    }

    /**
     * En desuso
     */
    public static function index_old()
    {
        $expedientes = Expediente::where('expediente_id', null)->get();
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

    /**
     * Método index replanteado para utilizar la db facade
     * Autor: Mariano Flores
     */
    public static function index()
    {
        $expediente = DB::table('expedientes')
                        ->where('expedientes.expediente_id', '=', null)
                        ->join('prioridad_expedientes', 'prioridad_expedientes.id', '=', 'expedientes.prioridad_id')
                        ->join('caratulas', 'caratulas.expediente_id', '=', 'expedientes.id')
                        ->join('extractos', 'extractos.id', '=', 'caratulas.extracto_id')
                        ->join('tipo_expedientes', 'tipo_expedientes.id', '=', 'expedientes.tipo_expediente')
                        ->join('areas', 'areas.id', '=', 'expedientes.area_actual_id')
                        ->join('iniciadores','caratulas.iniciador_id', 'iniciadores.id')
                        ->select(   'expedientes.id as id', 
                                    'prioridad_expedientes.descripcion as prioridad',
                                    'expedientes.nro_expediente',
                                    'extractos.descripcion as extracto',
                                    'expedientes.fecha as fecha_creacion',
                                    'tipo_expedientes.descripcion as tramite',
                                    'areas.descripcion as area_actual',
                                    DB::raw('ceil(expedientes.fojas / 200) as cantCuerpos'),
                                    'caratulas.id as caratula',
                                    'expedientes.fojas',
                                    'iniciadores.nombre as iniciador_nombre',
                                    'iniciadores.email',
                                    'caratulas.observacion'
                                )
                        ->orderBy('expedientes.id', 'DESC')
                        ->get();
        return $expediente;
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

    public static function listadoExpedientes_new($user_id, $estado, $bandeja)
    {
        //devuelve solo el 'id' del historial, del ultimo movimiento del expediente 
        $id_ultimos_movimientos = DB::table('historiales')
                        ->select(DB::raw('MAX(id) as id'))
                        ->groupBy('expediente_id');
                        
        //recupera el registro completo del historial del último movimiento del expediente
        $historial_ultimo_movimiento = DB::table('historiales')
                        ->select('areas.descripcion as descrippppp')
                        ->join('areas', 'areas.id', '=', 'historiales.area_origen_id')
                        ->joinSub($id_ultimos_movimientos, 'ultimo_movimiento_expediente', function($join)
                        {
                            $join->on('historiales.id', '=', 'ultimo_movimiento_expediente.id');
                        })->get();
        
                        return $historial_ultimo_movimiento;
        
        //recupera datos del área de origen
        $historial_con_areaOrigen = DB::table('areas')
                        ->joinSub($historial_ultimo_movimiento, 'h_u_m', function($join)
                        {
                            $join->on('id', '=', 'h_u_m.area_origen_id');
                        })
                        ->get();
                        return $historial_con_areaOrigen;                
                            
        /*
        $subAreaOrigen = DB::table('historiales')
                        ->select('expediente_id', DB::raw('MAX(fecha)'), 'areas.descripcion', 'estado')
                        ->join('areas', 'areas.id', '=', 'historiales.area_origen_id')
                        ->groupBy('historiales.expediente_id', 'fecha', 'areas.descripcion', 'estado');
                
        $subAreaDestino = DB::table('historiales')
                        ->select('expediente_id', DB::raw('MAX(fecha)'), 'areas.descripcion', 'estado')
                        ->join('areas', 'areas.id', '=', 'historiales.area_destino_id')
                        ->groupBy('historiales.expediente_id', 'areas.descripcion', 'estado');
                        

        switch($bandeja)
        {
            case "1": //Bandeja de entrada
                
                $query = DB::table('expedientes')
                        ->select('expedientes.id',
                                 'prioridad_expedientes.descripcion as prioridad',
                                 'expedientes.nro_expediente',
                                 'extractos.descripcion as extracto',
                                 'expedientes.fecha as fecha_creacion',
                                 'tipo_expedientes.descripcion as tramite',
                                 DB::raw('ceil(expedientes.fojas / 200) as cuerpos'),
                                 'caratulas.id as caratula',
                                 'expedientes.fojas',
                                 'areas.descripcion as area_actual',
                                 'areaOrigen.descripcion as area_origen',
                                 'expedientes.archivos as archivo'
                                 )
                        ->where('expedientes.expediente_id', '=', null)
                        ->joinSub($subAreaOrigen, 'areaOrigen', function($join)
                        {
                            $join->on('expedientes.id', '=', 'areaOrigen.expediente_id');
                        })     
                        ->join('prioridad_expedientes', 'prioridad_expedientes.id', '=', 'expedientes.prioridad_id')
                        ->join('caratulas', 'expedientes.id', '=', 'caratulas.expediente_id')
                        ->join('extractos', 'caratulas.extracto_id', '=', 'extractos.id')
                        ->join('tipo_expedientes', 'expedientes.tipo_expediente', '=', 'tipo_expedientes.id')
                        ->join('areas', 'areas.id', '=', 'expedientes.area_actual_id')
                        ->orderBy('expedientes.id')
                        ->get();
        return $query;
        */
    
        

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
                'observacion_pase'=>$exp->historiales->last()->observacion,
                'hora'=>$exp->historiales->last()->hora,
                'fecha'=>$exp->historiales->last()->fecha,
            ]);

        }
        /*
        * Filtro los Exp. por bandeja
        */

        #BANDEJA DE ENTRADA
        if ($bandeja == 1) {
            return $array_expediente->where('area_destino_id',$user->area_id)
                                    ->sortBy([
                                        ['prioridad', 'asc'],
                                        ['fecha', 'asc'],
                                        ['hora', 'asc']
                                    ])
                                    ->where('estado',$estado)->values();
        }


        #MIS EXPEDIENTE
        if ($bandeja == 3) {
            return $array_expediente->where('area_destino_id',$user->area_id)
                                    ->where('user_id',$user->id)
                                    ->whereIn('estado', [4,3])
                                    ->sortBy([
                                        ['prioridad', 'asc'],
                                        ['fecha', 'asc'],
                                        ['hora', 'asc']
                                    ])
                                    ->values();
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

    public static function listadoExpedientesSubsidioAporteNR()
    {
        $expedientes = DB::table('expedientes')
            ->join('prioridad_expedientes', 'expedientes.prioridad_id', '=', 'prioridad_expedientes.id')
            ->join('caratulas', 'expedientes.id', '=', 'caratulas.expediente_id')
            ->join('estado_expedientes', 'expedientes.estado_expediente_id', '=', 'estado_expedientes.id')
            ->join('extractos', 'caratulas.extracto_id', '=', 'extractos.id')
            ->join('tipo_expedientes', 'expedientes.tipo_expediente', '=', 'tipo_expedientes.id')
            ->select('expedientes.id',
                     'expedientes.nro_expediente as nroExpediente',
                     'expedientes.fecha as fecha_creacion',
                     'prioridad_expedientes.descripcion as prioridad',
                     'extractos.descripcion as extracto',
                     'tipo_expedientes.descripcion as tipoExpediente',
                     'expedientes.fojas as cantFojas',
                     DB::raw('truncate((expedientes.fojas / 200), 0) + 1 as cantCuerpos'))
            ->where('expedientes.tipo_expediente',3)
            ->orderBy('expedientes.id', 'DESC')
            ->get();
        return $expedientes;
    }
}
