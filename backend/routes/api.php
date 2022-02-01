<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\PagoController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\CedulaController;
use App\Http\Controllers\api\HistorialController;
use App\Http\Controllers\Api\IniciadorController;
use App\Http\Controllers\api\SolicitudController;
use App\Http\Controllers\api\ExpedienteController;
use App\Http\Controllers\api\NotificacionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|Cuando se implemente middleware usar auth:sanctum en lugar de auth:api
*/

Route::middleware('auth:sanctum')->group(function () {

    /******* USUARIO **************/
    Route::get('/salir',[LoginController::class, 'logout'] );
    Route::post('/validarPassword', [UserController::class, 'validar_password']);
    Route::post('/actualizaPassword', [UserController::class, 'actualiza_password']);
    Route::post('/userData', [UserController::class, 'getUserData']);

    /********** EXPEDIENTES **************/
    Route::get('/createExp',    [ExpedienteController::class, 'create']);
    Route::post('/buscar-expediente',   [ExpedienteController::class, 'buscarExpediente']);
    Route::get('/indexExp',             [ExpedienteController::class, 'index']);
    Route::post('/ListadoExp',       [ExpedienteController::class, 'bandeja']);
    Route::post('/expSubsidiosNoReintegrables', [ExpedienteController::class, 'expSubsidiosNoReintegrables']);
    Route::get('/all-motivos',       [ExpedienteController::class, 'indexMotivos']);


    /******* HISTORIAL **********/
    Route::post('/historial-expediente',    [HistorialController::class, 'store']);
    Route::post('/historial',               [HistorialController::class, 'create']);
    Route::post('/historialExp',            [HistorialController::class, 'historialExpediente']);
    Route::post('/update-estado',           [HistorialController::class, 'updateEstado']);
    Route::get('/mis-enviados',             [HistorialController::class, 'misEnviados']);

    /********** CEDULA ***********/
    Route::post('/contar-cedula',       [ExpedienteController::class, 'contar_cedulas']);
    Route::post('/store-cedula',    [CedulaController::Class, 'store']);

    /**** DESGLOSE Y ENGLOSE *****/
    Route::get('/indexExpPadres',       [ExpedienteController::class, 'indexExpPadres']);
    Route::post('/createDesgloceExp',       [ExpedienteController::class, 'createDesgloce']);
    Route::post('/desgloceExp',       [ExpedienteController::class, 'desgloce']);
    Route::post('/unionExp',       [ExpedienteController::class, 'union']);

    /**** INICIADORES ****/
    Route::get('/createTipoEntidad', [IniciadorController::class, 'create']);
    Route::post('/store-iniciador',   [IniciadorController::class, 'store']);
    Route::get('/index-iniciador',  [IniciadorController::Class, 'index']);
    Route::post('/edit-iniciador',  [IniciadorController::Class, 'edit']);
    Route::post('/update-iniciador',[IniciadorController::Class, 'update']);

    /**** SUBSIDIO Y NO REINTEGRABLES ****/
    Route::post('/expSubsidiosNoReintegrables', [ExpedienteController::class, 'expSubsidiosNoReintegrables']);

    /***** ALERTAS ***/
    Route::get('/contarExp',         [ExpedienteController::class, 'contadorBandejaEntrada']);
    Route::post('/contarSubsidioAporteNR', [NotificacionController::class, 'contadorSubsidioAporteNR']);
    Route::get('/notificacion',               [NotificacionController::class, 'index']);
    Route::get('/notificacion-cerrar',               [NotificacionController::class, 'update']);


    /*** NUEVO EXPEDIENTE ***/
    Route::post('/nroExp',              [ExpedienteController::class, 'createNroExpediente']);
    Route::post('/storeExp',            [ExpedienteController::class, 'store']);
    Route::post('/zip',               [ExpedienteController::class, 'descargarZip']);

    //Rutas de prueba para mostrar implementacion de permisos -- se pueden borrar rutas y métodos en controladores
    Route::get('/prueba',    [ExpedienteController::class, 'pruebaNormal'])->middleware(['auth:sanctum', 'abilities:normalito']);
    Route::get('/prueba2',    [ExpedienteController::class, 'pruebaEnglose'])->middleware(['auth:sanctum', 'abilities:englose:desglose']);
    //Rutas de prueba para mostrar implementacion de permisos -- se pueden borrar rutas y métodos en controladores

});

// Rutas que no se deben proteger
Route::get('/login',[LoginController::class, 'showLoginForm'] );
Route::post('/login',[LoginController::class, 'authenticate_new'] ); // reemplazar por Route::post('/login',[LoginController::class, 'authenticate_new'] );

Route::get('/all-expedientes',      [ExpedienteController::class, 'AllExpedientes']);

