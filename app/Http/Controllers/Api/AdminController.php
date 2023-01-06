<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\User;
use App\Models\Proveedora;
use App\Models\Entrada;
use App\Models\Producto;
use App\Models\Salida;
use App\Models\Saldo;
use App\Models\Ubicacione;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use stdClass;

class AdminController extends Controller
{
    private $password = 'Kardex_2022_.';
    
    public function index_users(Request $request)
    {
        $user_loged = Auth()->User();

        $datos = $request->validate([
            'password' => 'required|string|min:10|max:100'
        ]);

        if($datos['password'] != $this->password){
           return response()->json(['error'=>401,'response'=>'No autorizado'],401);
        }

        $usuarios = [];
        $usuarios = User::all();

        return response()->json(['error'=>0,'usuarios'=>$usuarios],200);

    }

    public function delete_user(Request $request)
    {
        $user_loged = Auth()->User();

        $datos = $request->validate([
            'password' => 'required|string|min:10|max:100',
            'user_id' => 'required|numeric|min:1'
        ]);

        if($datos['password'] != $this->password){
           return response()->json(['error'=>401,'response'=>'No autorizado'],401);
        }

        $bool_user = User::where('id',$datos['user_id'])->exists();
        if(!$bool_user){
           return response()->json(['error'=>300,'response'=>'El usuario no existe!'],200);
        }

        $categoria_num = Categoria::where('user_id',$datos['user_id'])->delete();
        $proveedora_num = Proveedora::where('user_id',$datos['user_id'])->delete();
        $ubicaciones_num = Ubicacione::where('user_id',$datos['user_id'])->delete();
        $producto_num = Producto::where('user_id',$datos['user_id'])->delete();
        $entradas_num = Entrada::where('user_id',$datos['user_id'])->delete();
        $salidas_num = Salida::where('user_id',$datos['user_id'])->delete();
        $saldo_num = Saldo::where('user_id',$datos['user_id'])->delete();
        $usuario_num = User::where('id',$datos['user_id'])->delete();
        

        return response()->json(['error'=>0,'response'=>'Usuario borrado'],200);

    }

    public function index_data_user(Request $request){

        $user_loged = Auth()->User();

        $datos = $request->validate([
            'password' => 'required|string|min:10|max:100',
            'user_id' => 'required|numeric|min:1'
        ]);

        if($datos['password'] != $this->password){
           return response()->json(['error'=>401,'response'=>'No autorizado'],401);
        }

        $bool_user = User::where('id',$datos['user_id'])->exists();
        if(!$bool_user){
           return response()->json(['error'=>300,'response'=>'El usuario no existe!'],200);
        }

        $categoria_num = Categoria::where('user_id',$datos['user_id'])->count();
        $proveedora_num = Proveedora::where('user_id',$datos['user_id'])->count();
        $ubicaciones_num = Ubicacione::where('user_id',$datos['user_id'])->count();
        $producto_num = Producto::where('user_id',$datos['user_id'])->count();
        $entradas_num = Entrada::where('user_id',$datos['user_id'])->count();
        $salidas_num = Salida::where('user_id',$datos['user_id'])->count();
        $saldo_num = Saldo::where('user_id',$datos['user_id'])->count();
        $usuario = User::find($datos['user_id']);

        $consulta = new stdClass();
        $consulta->user = $usuario;
        $consulta->categorias = $categoria_num;
        $consulta->proveedoras = $proveedora_num;
        $consulta->ubicaciones = $ubicaciones_num;
        $consulta->producto = $producto_num;
        $consulta->entradas = $entradas_num;
        $consulta->salidas = $salidas_num;
        $consulta->saldo = $saldo_num;
        
        return response()->json(['error'=>0,'response'=>$consulta],200);

    }


}