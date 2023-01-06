<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

##  Administrador ##
Route::get('/index_users',[Api\AdminController::class,'index_users'])->middleware('auth:sanctum');
Route::get('/index_data_user',[Api\AdminController::class,'index_data_user'])->middleware('auth:sanctum');
Route::put('/delete_user',[Api\AdminController::class,'delete_user'])->middleware('auth:sanctum');


##  Usuario ##
Route::post('/restablecer',[Api\UserController::class,'restablecer']);
Route::post('/recuperar',[Api\UserController::class,'recuperar']);
Route::post('/update_password',[Api\UserController::class,'update_password'])->middleware('auth:sanctum');
Route::post('/registro',[Api\UserController::class,'registro']);
Route::post('/login',[Api\UserController::class,'login']);
Route::get('/index',[Api\UserController::class,'index'])->middleware('auth:sanctum');
Route::get('/logout',[Api\UserController::class,'logout'])->middleware('auth:sanctum');
Route::get('/index_reporte',[Api\UserController::class,'index_reporte'])->middleware('auth:sanctum');

## Categorias ##
Route::get('/index_categorias',[Api\CategoriaController::class,'index'])->middleware('auth:sanctum');
Route::post('/store_categoria',[Api\CategoriaController::class,'store'])->middleware('auth:sanctum');
Route::put('/delete_categoria',[Api\CategoriaController::class,'delete'])->middleware('auth:sanctum');

## Productos ##
Route::get('/index_productos',[Api\ProductoController::class,'index'])->middleware('auth:sanctum');
Route::post('/store_producto',[Api\ProductoController::class,'store'])->middleware('auth:sanctum');
Route::put('/update_producto',[Api\ProductoController::class,'update'])->middleware('auth:sanctum');
Route::put('/delete_producto',[Api\ProductoController::class,'delete'])->middleware('auth:sanctum');

## Proveedores ##
Route::get('/index_proveedores',[Api\ProveedorController::class,'index'])->middleware('auth:sanctum');
Route::post('/store_proveedor',[Api\ProveedorController::class,'store'])->middleware('auth:sanctum');
Route::put('/update_proveedor',[Api\ProveedorController::class,'update'])->middleware('auth:sanctum');
Route::put('/delete_proveedor',[Api\ProveedorController::class,'delete'])->middleware('auth:sanctum');

## Ubicaciones ##
Route::get('/index_ubicaciones',[Api\UbicacionController::class,'index'])->middleware('auth:sanctum');
Route::post('/store_ubicacion',[Api\UbicacionController::class,'store'])->middleware('auth:sanctum');
Route::put('/update_ubicacion',[Api\UbicacionController::class,'update'])->middleware('auth:sanctum');
Route::put('/delete_ubicacion',[Api\UbicacionController::class,'delete'])->middleware('auth:sanctum');

## Entradas ##
Route::get('/index_entradas',[Api\EntradaController::class,'index'])->middleware('auth:sanctum');
Route::post('/store_entrada',[Api\EntradaController::class,'store'])->middleware('auth:sanctum');
Route::put('/delete_entrada',[Api\EntradaController::class,'delete'])->middleware('auth:sanctum');
Route::get('/latest_entrada',[Api\EntradaController::class,'latest'])->middleware('auth:sanctum');

## Salidas ##
Route::get('/index_salidas',[Api\SalidaController::class,'index'])->middleware('auth:sanctum');
Route::post('/store_salida',[Api\SalidaController::class,'store'])->middleware('auth:sanctum');
Route::put('/delete_salida',[Api\SalidaController::class,'delete'])->middleware('auth:sanctum');


