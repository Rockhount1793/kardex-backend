<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Proveedora;
use App\Models\Entrada;
use App\Models\Saldo;
use App\Models\Salida;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index()
    {

        $user_loged = auth()->user();

        $productos = [];
        $productos = Producto::where('user_id',$user_loged->id)->take(500)->orderBy('created_at','DESC')->get();

        return response()->json(['error'=>0,'productos'=>$productos],200 );
    }

    public function store(Request $request)
    {

        $user_loged = Auth()->User();

        $datos = $request->validate([
            'nombre'=>'required|string|min:3|max:100',
            'marca'=>'required|string|min:3|max:100',
            'referencia'=>'required|string|min:1|max:100',
            'margen_ganancia'=>'required|numeric|min:1|max:5000',
            'codigo'=>'required|string|min:1|max:100'
        ]);

        //if($request->bool_imagen){

        //    $validator_image = Validator::make(request()->input(),[
        //        'file' => 'image|mimes:jpg,png,svg|max:2048|dimensions:min_width=300,min_height=300,max_width=1000,max_height=1000',
        //    ]);

        //    if($validator_image->fails()){
        //        return response()->json(['error'=>99,'respuesta','Formato de imagen incorrecto'],403);
        //    }

        //}

        $count_productos = 0;
        $count_productos = Producto::where('user_id',$user_loged->id)->count();

        if($count_productos >= 1000){
            return response()->json(['error'=>300,'response'=>'Máximo 1000 productos'],200);
        }

        //$imagen = 'default.png';

        //if($request->bool_imagen){

        //    $path = Storage::putFile('public/images_productos', $request->file('file'));
        //    $name = $request->file('file')->getClientOriginalName();
        //    $extension = $request->file('file')->extension();

        //    $imagen = $request->file('file')->hashName();
        //
        //}

        $producto = Producto::create([
            'user_id'=>$user_loged->id,
            'margen_ganancia'=> $datos['margen_ganancia'],
            'nombre'=> $datos['nombre'],
            'marca'=>$datos['marca'],
            'codigo' =>$datos['codigo'],
            'referencia'=>$datos['referencia']
        ]);

        return response()->json(['error'=>0,'producto'=>$producto], 200);

    }

    public function update(Request $request)
    {
        $user_loged = Auth()->User();

        $datos = $request->validate([
            'id'=>'required|numeric|min:1',
            'nombre'=>'required|string|min:3|max:100',
            'marca'=>'required|string|min:3|max:100',
            'referencia'=>'required|string|min:1|max:100',
            'margen_ganancia'=>'required|numeric|min:1|max:5000',
            'codigo'=>'required|string|min:1|max:100'
        ]);

        $bool_producto = Producto::where('user_id',$user_loged->id)->where('id',$datos['id'])->exists();

        if(!$bool_producto){
            return response()->json(['error'=>403,'response'=>'Consulta equivocada'],403);
        }

        $producto = Producto::find($datos['id']);
        $producto->nombre = $datos['nombre'];
        $producto->marca = $datos['marca'];
        $producto->referencia = $datos['referencia'];
        $producto->margen_ganancia = $datos['margen_ganancia'];
        $producto->codigo = $datos['codigo'];
        $producto->save();

        return response()->json(['error'=>0,'producto'=>$producto],200);
    }

    public function delete(Request $request)
    {

        $user_loged = auth()->user();

        $datos = $request->validate([
            'producto_id' => 'required|numeric|min:1'
        ]);

        $bool_entrada = Entrada::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->exists();
        $bool_salida = Salida::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->exists();
       
        if($bool_entrada){
            return response()->json(['error'=>300,'response'=>'El producto tiene entradas registradas'],200);
        }

        if($bool_salida){
            return response()->json(['error'=>400,'response'=>'El producto tiene salidas registradas'],200);
        }

        $bool_producto = Producto::where('id',$datos['producto_id'])->where('user_id',$user_loged->id)->exists();

        if($bool_producto){
        
            $saldo = Saldo::where('producto_id',$datos['producto_id'])->delete();
            $producto = Producto::where('id',$datos['producto_id'])->delete();
            
            return response()->json(['error'=>0,'response'=>'Producto eliminado'],200 );

        }else{

            return response()->json(['error'=>403,'response'=>'Producto no encontrado!'],403);
        
        }

    }

}
