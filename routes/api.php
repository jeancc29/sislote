<?php

use Illuminate\Http\Request;
use Faker\Generator as Faker;
use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
use App\Blockslotteries;
use App\Blocksplays;
use App\Stock;
use App\Tickets;
use App\Cancellations;
use App\Days;
use App\Payscombinations;
use App\Awards;
use App\Draws;
use App\Branches;
use App\Users;
use App\Roles;
use App\Commissions;
use App\Permissions;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

date_default_timezone_set("America/Santiago");



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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


//Route::get('/principal', 'PrincipalController@index');
//Route::get('/principal', 'PrincipalController@index');

//Route::apiResource('principal', 'PrincipalController');
// Route::post('/realtime', 'RealtimeController@index');
Route::post('/realtime/todos', 'RealtimeController@todos');
Route::post('/acceder', 'LoginController@accederApi');
Route::post('/cambiarServidorApi', 'LoginController@cambiarServidorApi');

Route::get('/dashboard', 'DashboardController@index');

Route::get('/principal', 'PrincipalController@index');
Route::post('/principal/indexPost', 'PrincipalController@indexPost');
// Route::post('/principal/indexPostPrueba', 'PrincipalController@indexPostPrueba');
Route::post('/principal/guardar', 'PrincipalController@store');
Route::post('/principal/guardarMovil', 'PrincipalController@storeMovil');
Route::post('/principal/storeMobileV2', 'PrincipalController@storeMobileV2');
Route::post('/principal/montodisponible', 'PrincipalController@montodisponible');
Route::post('/principal/pruebahttp', 'PrincipalController@pruebahttp');
Route::post('/principal/pagar', 'PrincipalController@pagar');
Route::post('/principal/buscarTicket', 'PrincipalController@buscarTicket');
Route::post('/principal/buscarTicketAPagar', 'PrincipalController@buscarTicketAPagar');
Route::post('/principal/duplicar', 'PrincipalController@duplicar');
Route::post('/principal/cancelar', 'PrincipalController@cancelar');
Route::post('/principal/cancelarMovil', 'PrincipalController@cancelarMovil');
Route::post('/principal/eliminar', 'PrincipalController@eliminar');
Route::post('/principal/createIdTicket', 'PrincipalController@createIdTicket');

Route::post('/imagen/guardar', 'PrincipalController@imagen');
Route::post('/principal/sms', 'PrincipalController@sms');

Route::get('/loterias', 'LotteriesController@index');
Route::post('/loterias/guardar', 'LotteriesController@store');
Route::post('/loterias/eliminar', 'LotteriesController@destroy');

Route::get('/bancas', 'BranchesController@index');
Route::get('/v2/bancas', 'BranchesController@indexV2');
Route::post('/bancas/get', 'BranchesController@show');
Route::post('/bancas/getDatos', 'BranchesController@getDatos');
Route::post('/bancas/guardar', 'BranchesController@store');
Route::post('/bancas/v2/guardar', 'BranchesController@storeV2');
Route::post('/bancas/eliminar', 'BranchesController@destroy');
Route::post('/bancas/search', 'BranchesController@search');
Route::post('/bancas/getVentasDelDia', 'BranchesController@getVentasDelDia');

Route::get('/usuarios', 'UsersController@index');
Route::post('/usuarios/guardar', 'UsersController@store');
Route::post('/usuarios/eliminar', 'UsersController@destroy');
Route::post('/usuarios/sesiones', 'UserssesionsController@buscar');
Route::post('/v2/usuarios/sesiones', 'UserssesionsController@buscarV2');
Route::post('/usuarios/search', 'UsersController@search')->name("users.search");

Route::get('/horarios', 'HorariosController@index');
Route::post('/horarios/normal/guardar', 'HorariosController@store');

Route::get('/premios', 'AwardsController@index');
Route::post('/premios/buscarPorFecha', 'AwardsController@buscarPorFecha');
Route::post('/premios/erase', 'AwardsController@erase');
Route::post('/premios/guardar', 'AwardsController@store');

Route::get('/bloqueos', 'BlockslotteriesController@index');
Route::post('/bloqueos/general/loterias/guardar', 'BlockslotteriesController@storeGeneral');
Route::post('/bloqueos/loterias/guardar', 'BlockslotteriesController@store');
Route::post('/bloqueos/jugadas/guardar', 'BlocksplaysController@store');
Route::post('/bloqueos/jugadas/eliminar', 'BlocksplaysController@destroy');
Route::post('/bloqueos/general/jugadas/guardar', 'BlocksplaysController@storeGeneral');
Route::post('/bloqueos/loterias/buscar', 'BlockslotteriesController@buscar');
Route::post('/bloqueos/loterias/eliminar', 'BlockslotteriesController@destroy');
Route::post('/bloqueosgenerales/loterias/eliminar', 'BlocksgeneralsController@destroy');
Route::post('/bloqueosgenerales/jugadas/eliminar', 'BlocksplaysgeneralsController@destroy');

Route::post('/bloqueos/general/sucias/guardar', 'BlocksdirtygeneralsController@store');
Route::post('/bloqueos/general/sucias/eliminar', 'BlocksdirtygeneralsController@destroy');
Route::post('/bloqueos/sucias/guardar', 'BlocksdirtyController@store');
Route::post('/bloqueos/sucias/eliminar', 'BlocksdirtyController@destroy');

Route::post('/reportes/monitoreo/', 'ReportesController@monitoreo');
Route::post('/reportes/monitoreoMovil/', 'ReportesController@monitoreoMovil');
Route::post('/reportes/getTicketById/', 'ReportesController@getTicketById');
Route::post('/reportes/v2/getTicketById/', 'ReportesController@getTicketByIdV2');
Route::post('/reportes/ventas/', 'ReportesController@ventas')->name("reporte.ventas");
Route::post('/reportes/jugadas/', 'ReportesController@jugadas');
Route::post('/reportes/historico/', 'ReportesController@historico');
Route::post('/reportes/v2/historico/', 'ReportesController@historicoV2');
Route::get('/historico/', 'ReportesController@historicoApi')->name("historicoApi");
Route::post('/reportes/ventasporfecha/', 'ReportesController@ventasporfecha');
Route::post('/reportes/v2/ventasPorfecha/', 'ReportesController@ventasPorfechaV2')->name("reportes.ventasPorfechaV2");
Route::post('/reportes/ticketsPendientesDePago/', 'ReportesController@ticketsPendientesDePago');
Route::post('/reportes/ticketsPendientesDePagoIndex/', 'ReportesController@ticketsPendientesDePagoIndex');
Route::post('/reportes/reporteJugadas/', 'ReportesController@reporteJugadas')->name("reporte.jugadas");

Route::get('/entidades', 'EntityController@index');
Route::post('/entidades/guardar', 'EntityController@store');
Route::post('/entidades/eliminar', 'EntityController@destroy');

Route::get('/transacciones', 'TransactionsController@index');
Route::get('/transacciones/grupo', 'TransactionsController@grupo');
Route::post('/transacciones/saldo', 'TransactionsController@saldo');
Route::post('/transacciones/guardar', 'TransactionsController@store');
Route::post('/transacciones/buscar', 'TransactionsController@buscar');
Route::post('/transacciones/buscarTransaccion', 'TransactionsController@buscarTransaccion');

Route::post('/monitoreo/tickets', 'MonitoreoController@monitoreo');
Route::post('/balance/bancas', 'BalancesController@index');
// Route::post('/balance/v2/bancas', 'BalancesController@indexV2');
Route::post('/balance/bancos', 'BalancesController@bancos');
Route::post('/versiones', 'AndroidversionsController@index');
Route::post('/versiones/guardar', 'AndroidversionsController@store');
Route::post('/versiones/publicar', 'AndroidversionsController@publicar');
Route::post('/versiones/publicada', 'AndroidversionsController@publicada');

Route::post('/prestamos/guardar', 'LoansController@store');
Route::post('/prestamos/getPrestamo', 'LoansController@getPrestamo');
Route::post('/prestamos/cobrar', 'LoansController@cobrar');
Route::post('/prestamos/aplazarCuota', 'LoansController@aplazarCuota');
Route::post('/prestamos/eliminar', 'LoansController@eliminar');

Route::get('/monedas', 'CoinsController@index');
Route::post('/monedas/guardar', 'CoinsController@store');
Route::post('/monedas/eliminar', 'CoinsController@destroy');
Route::post('/monedas/pordefecto', 'CoinsController@pordefecto');

Route::post('/servidor/servidorExiste', 'ServerController@servidorExiste');

Route::get('/notifications', 'NotificationController@index');
Route::post('/notifications/guardar', 'NotificationController@store');

Route::get('/grupos', 'GroupController@index');
Route::post('/grupos/guardar', 'GroupController@store');
Route::post('/grupos/eliminar', 'GroupController@destroy');
Route::post('/grupos/pordefecto', 'GroupController@pordefecto');


Route::post('/ajustes', 'SettingsController@index')->name("settings.index");
Route::post('/ajustes/guardar', 'SettingsController@store')->name("settings.store");