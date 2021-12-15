<?php

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
    Route::get('/salir',[LoginController::class, 'logout'] );
});

Route::get('/verificar',[LoginController::class, 'verificar'] );

//EXPEDIENTE////////////////////////////////////////////////////////////////////////
Route::get('/indexExp',             [ExpedienteController::class, 'index']);
Route::get('/createExp',            [ExpedienteController::class, 'create']);
Route::post('/storeExp',            [ExpedienteController::class, 'store']);
Route::get('/updateExp',            [ExpedienteController::class, 'update']);
Route::post('/nroExp',              [ExpedienteController::class, 'createNroExpediente']);
Route::post('/showExp',             [ExpedienteController::class, 'show']);
Route::post('/indexExpArea',        [ExpedienteController::class, 'indexPorAreas']);
Route::post('/buscar-expediente',   [ExpedienteController::class, 'buscarExpediente']);
Route::get('/all-expedientes',      [ExpedienteController::class, 'AllExpedientes']);
//Route::get('/zip',                  [ExpedienteController::class, 'descargarZip']);//TODO ruta back 8000
Route::post('/zip',               [ExpedienteController::class, 'descargarZip']);
Route::get('/show-detalle',        [ExpedienteController::class, 'showDetalleExpediente']);
////////////////////////////////////////////////////////////////////////////////////
/// NUEVO INICIADOR
Route::get('/createTipoEntidad',      [IniciadorController::class, 'create']);
Route::post('/storeIniciador',      [IniciadorController::class, 'store']);

//BANDEJAS//////////////////////////////////////////////////////////////////////////////////

Route::post('/ListadoExp',       [ExpedienteController::class, 'bandeja']);
Route::post('/contarExp',         [ExpedienteController::class, 'contadorBandejaEntrada']);

Route::post('/contarSubsidioAporteNR', [ExpedienteController::class, 'contadorSubsidioAporteNR']);
Route::post('/expSubsidiosNoReintegrables', [ExpedienteController::class, 'expSubsidiosNoReintegrables']);

///////////////////////////////////////////////////////////////////////////////////

//PRUEBA UNION-DESGLOCE////////////////////////////////////////////////////////////////////////////
Route::post('/unionExp',       [ExpedienteController::class, 'union']);
Route::get('/indexExpPadres',       [ExpedienteController::class, 'indexExpPadres']);
Route::post('/createDesgloceExp',       [ExpedienteController::class, 'createDesgloce']);
Route::post('/desgloceExp',       [ExpedienteController::class, 'desgloce']);
//////////////////////////////////////////////////////////////////////////////////////////////////
Route::post('/historial',                [HistorialController::class, 'create']);
Route::post('/historial-expediente',     [HistorialController::class, 'store']);
Route::post('/update-estado',     [HistorialController::class, 'updateEstado']);
Route::post('/historialExp',     [HistorialController::class, 'historialExpediente']);
Route::post('/mis-enviados',     [HistorialController::class, 'misEnviados']);

Route::get('/indexUser', [UserController::class, 'index']);
Route::get('/createUser', [UserController::class, 'create']);
Route::post('/storeUser', [UserController::class, 'store']);
Route::get('/editUser', [UserController::class, 'edit']);
Route::post('/updateUser', [UserController::class, 'update']);
Route::get('/bajaUser', [UserController::class, 'delete']);
Route::get('/restoreUser', [UserController::class, 'restore']);

Route::post('/validarPassword', [UserController::class, 'validar_password']);
Route::post('/actualizaPassword', [UserController::class, 'actualiza_password']);

Route::get('/login',[LoginController::class, 'showLoginForm'] );
Route::post('/login',[LoginController::class, 'authenticate'] );

///////INICIADORES//////////////////////////////////////////////////////////////////////////
Route::get('/index-iniciador',  [IniciadorController::Class, 'index']);
Route::post('/store-iniciador', [IniciadorController::Class, 'store']);
Route::post('/show-iniciador',  [IniciadorController::Class, 'show']);
Route::post('/edit-iniciador',  [IniciadorController::Class, 'edit']);
Route::post('/update-iniciador',[IniciadorController::Class, 'update']);
////////////////////////////////////////////////////////////////////////////////////////////

///////SOLICITUDES//////////////////////////////////////////////////////////////////////////
Route::get('/index-solicitud',      [SolicitudController::Class, 'index']);
Route::post('/store-solicitud',     [SolicitudController::Class, 'store']);
Route::post('/show-solicitud',      [SolicitudController::Class, 'show']);
Route::post('/update-solicitud',    [SolicitudController::Class, 'update']);
////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/prueba-codBarrra',[ExpedienteController::class, 'codigoBarra'] );

///////CEDULAS//////////////////////////////////////////////////////////////////////////////
Route::get('/index-cedula',      [CedulaController::Class, 'index']);
Route::post('/store-cedula',    [CedulaController::Class, 'store']);
Route::post('/create-cedula',    [CedulaController::Class, 'create']);
Route::post('/edit-cedula',    [CedulaController::Class, 'edit']);
Route::post('/update-cedula',    [CedulaController::Class, 'update']);
////////////////////////////////////////////////////////////////////////////////////////////

///////NOTIFICACIONES///////////////////////////////////////////////////////////////////////
Route::get('/index-notificacion',       [NotificacionController::Class, 'index']);
Route::post('/store-notificacion',      [NotificacionController::Class, 'store']);
Route::post('/update-notificacion',     [NotificacionController::Class, 'update']);
////////////////////////////////////////////////////////////////////////////////////////////

///////PAGOS////////////////////////////////////////////////////////////////////////////////
Route::get('/index-pagos',      [PagoController::Class, 'index']);
Route::post('/store-pagos',     [PagoController::Class, 'store']);
Route::post('/update-pagos',    [PagoController::Class, 'update']);
////////////////////////////////////////////////////////////////////////////////////////////
Route::post('/subir-zip',    [ExpedienteController::class, 'validarZip']);
