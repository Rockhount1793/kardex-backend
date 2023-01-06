<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
})->name('Bienvenida');

//Route::get('/restablecer', function () {
//    
//    $contenido = ['restablecer','Restablecer Contraseña','nzcaicedo@gmail.com','ONZNIMP39RBWEEXQTEUJ','n.png'];
//
//    return view('restablecer')->with(['contenido'=>$contenido]);
//
//})->name('Restablecer');