<?php

namespace App\Http\Controllers\api;

use app\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return "Vista Login.";
    }

    public function logout()
    {
        $user = User::findOrFail(auth()->user()->id);
        $user->tokens()->delete();
        return ('usuario deslogueado');
    }

    /**
     * Método para autenticar un usuario y loquearlo en el sistema,
     * genera un token para utilizar la API con habilidades dependiendo del área
     * @param: cuil
     * Autor: Mariano Flores
     */
    public function authenticate_new(LoginRequest $request)
    {
        $user = ModelsUser::where("cuil", "=", "$request->cuil")->first();
        //return $user->id;
        if(isset($user->id))
        {
            $areas_creaExpediente_acumula = array(13);
            $areas_agregarIniciador = array(15);
            $areas_agregarCedulas = array(14);
            
            $usuarios_agregaIniciador = array(36);              //user_id

            # $user->tokens()->delete(); // comentar ésta línea si se requiere asignar más de un token al usuario
            
            if(Hash::check($request->password, $user->password))
            {
                /*if(in_array($user->area_id, $areas_englose))
                {
                    $token = $user->createToken('englose_desglose', ['englose_desglose'])->plainTextToken;
                    return response()->json([
                        "status" => true,
                        "mensaje" => "usuario logueado exitosamente",
                        "nombre_apellido" => $user->persona->nombre ." ". $user->persona->apellido,
                        "cuil" => $user->cuil,
                        "id" => $user->area->id,
                        "area" => $user->area->descripcion,
                        "cargo" => $user->tipouser->descripcion,
                        "access_token" => $token,
                        "token_type" => "englose_desglose"
                    ], 200);
                }*/

                if((in_array($user->area_id, $areas_creaExpediente_acumula)) && (in_array($user->id, $usuarios_agregaIniciador)))
                {
                    $token = $user->createToken('crear_expediente_e_iniciador', ['agregar_iniciador', 'crear_expediente', 'acumular_desglosar'])->plainTextToken;
                    return response()->json([
                        "status" => true,
                        "mensaje" => "usuario logueado exitosamente",
                        "nombre_apellido" => $user->persona->nombre ." ". $user->persona->apellido,
                        "cuil" => $user->cuil,
                        "id" => $user->area->id,
                        "area" => $user->area->descripcion,
                        "cargo" => $user->tipouser->descripcion,
                        "access_token" => $token,
                        "token_type" => "agregar_iniciador - crea_expediente - acumular_desglosar"
                    ], 200);
                }
                else if(in_array($user->area_id, $areas_agregarIniciador))
                    {
                        $token = $user->createToken('agregar_iniciador', ['agregar_iniciador'])->plainTextToken;
                        return response()->json([
                            "status" => true,
                            "mensaje" => "usuario logueado exitosamente",
                            "nombre_apellido" => $user->persona->nombre ." ". $user->persona->apellido,
                            "cuil" => $user->cuil,
                            "id" => $user->area->id,
                            "area" => $user->area->descripcion,
                            "cargo" => $user->tipouser->descripcion,
                            "access_token" => $token,
                            "token_type" => "agregar_iniciador"
                        ], 200);
                    }
                

                if(in_array($user->area_id, $areas_creaExpediente_acumula))
                {
                    $token = $user->createToken('crear_expediente_acumula', ['crear_expediente'. 'acumular_desglosar'])->plainTextToken;
                    return response()->json([
                        "status" => true,
                        "mensaje" => "usuario logueado exitosamente",
                        "nombre_apellido" => $user->persona->nombre ." ". $user->persona->apellido,
                        "cuil" => $user->cuil,
                        "id" => $user->area->id,
                        "area" => $user->area->descripcion,
                        "cargo" => $user->tipouser->descripcion,
                        "access_token" => $token,
                        "token_type" => "crear_expediente - acumular_desglosar"
                    ], 200);
                }

                if(in_array($user->area_id, $areas_agregarCedulas))
                {
                    $token = $user->createToken('agregar_cedulas', ['agregar_cedulas'])->plainTextToken;
                    return response()->json([
                        "status" => true,
                        "mensaje" => "usuario logueado exitosamente",
                        "nombre_apellido" => $user->persona->nombre ." ". $user->persona->apellido,
                        "cuil" => $user->cuil,
                        "id" => $user->area->id,
                        "area" => $user->area->descripcion,
                        "cargo" => $user->tipouser->descripcion,
                        "access_token" => $token,
                        "token_type" => "agregar_cedulas"
                    ], 200);
                }
                
                else
                {
                    $token = $user->createToken('normal', ['normalito'])->plainTextToken;
                    return response()->json([
                        "status" => true,
                        "mensaje" => "usuario logueado exitosamente",
                        "nombre_apellido" => $user->persona->nombre ." ". $user->persona->apellido,
                        "cuil" => $user->cuil,
                        "id" => $user->area->id,
                        "area" => $user->area->descripcion,
                        "cargo" => $user->tipouser->descripcion,
                        "access_token" => $token,
                        "token_type" => "normal"
                    ], 200);
                }
            }
            else
            {
                return response()->json([
                    "status" => false,
                    "mensaje" => "contraseña incorrecta",
                ], 404);
            }
        }
        else
        {
            return response()->json([
                "status" => false,
                "mensaje" => "usuario y/o contraseña incorrecta",
            ], 404);       
        }   
    }
}