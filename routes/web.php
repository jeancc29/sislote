<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/login', 'LoginController@index')->name('login');
Route::get('/cerrar', 'LoginController@cerrarSesion')->name('cerrarSesion');
Route::post('/login/acceder', 'LoginController@acceder')->name('login.acceder');

Route::get('/loterias', 'LotteriesController@index')->name('loterias');
Route::get('/loterias/bloqueos', 'LotteriesController@bloqueos')->name('loterias.bloqueos');
Route::get('/principal', 'PrincipalController@index')->name('principal');
Route::get('/principal/ticket', 'PrincipalController@ticket')->name('principal.ticket');
Route::get('/principal/pruebahttp', 'PrincipalController@pruebahttp')->name('principal.pruebahttp');


Route::get('/premios', 'AwardsController@index')->name('premios');

Route::get('/bancas', 'BranchesController@index')->name('bancas');


Route::get('/usuarios', 'UsersController@index')->name('usuarios');
Route::get('/usuarios/sesiones', 'UserssesionsController@index')->name('usuarios.sesiones');
Route::get('/horarios', 'HorariosController@index')->name('horarios');
Route::get('/', 'DashboardController@index')->name('dashboard');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/bloqueos', 'BlockslotteriesController@index')->name('bloqueos');


Route::get('/reportes/jugadas', 'ReportesController@jugadas')->name('reportes.jugadas');
Route::get('/reportes/historico', 'ReportesController@historico')->name('reportes.historico');
Route::get('/reportes/ventasporfecha', 'ReportesController@ventasporfecha')->name('reportes.ventasporfecha');

Route::get('/entidades', 'EntityController@index')->name('entidades');
Route::get('/transacciones/grupo', 'TransactionsController@grupo')->name('transacciones.grupo');
Route::get('/transacciones', 'TransactionsController@index')->name('transacciones');

Route::get('/monitoreo/tickets', 'MonitoreoController@tickets')->name('monitoreo.tickets');
Route::get('/politica', 'PoliticaPrivacidadController@index')->name('politica');


Route::get('/balance/bancas', 'BalancesController@index')->name('balance.bancas');
Route::get('/balance/bancos', 'BalancesController@bancos')->name('balance.bancos');
Route::get('/prestamos', 'LoansController@index')->name('prestamos');
Route::get('/versiones', 'AndroidversionsController@index')->name('versiones');
Route::get('/test1', 'DaysController@test1')->name('prueba1');
Route::get('/test2', 'DaysController@test2')->name('prueba2');
Route::get('/test3', 'DaysController@test3')->name('prueba3');