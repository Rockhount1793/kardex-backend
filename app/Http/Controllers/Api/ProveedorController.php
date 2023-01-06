<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedora;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Entrada;
use App\Models\Salida;

class ProveedorController extends Controller
{

    public function index()
    {
        $user = Auth()->User();
       
        $proveedores = [];
        $proveedores = Proveedora::where('user_id','=',$user->id)->take(500)->orderBy('created_at','DESC')->get();

        return response()->json(['error'=>0,'proveedores'=>$proveedores]);
       
    }

    public function store(Request $request)
    {
        $user_loged = Auth()->User();

        $datos = $request->validate([
            'nombre' => 'required|string|min:5|max:100',
            'contacto'=>'required|string|min:3|max:100',
            'email'=>'required|email|min:6|max:100',
            'direccion'=>'required|string|min:3|max:100',
            'telefono'=>'required|string|min:3|max:100'

        ]);

        $count_proveedores = 0;
        $count_proveedores = Proveedora::where('user_id',$user_loged->id)->count();

        if($count_proveedores >= 500){
            return response()->json(['error'=>300,'response'=>'Máximo 500 proveedores'],200);
        }

        $pro = Proveedora::create([
            'nombre' => $datos['nombre'],
            'contacto' => $datos['contacto'],
            'direccion' => $datos['direccion'],
            'telefono' => $datos['telefono'],
            'email' => $datos['email'],
            'user_id' => $user_loged->id
        ]);

        return response()->json(['error'=>0,'proveedor'=>$pro], 200);
    }

    public function update(Request $request)
    {
        $user_loged = Auth()->User();
        
        $datos = $request->validate([
            'id'=>'required|numeric|min:1',
            'nombre' => 'required|string|min:5|max:100',
            'contacto'=>'required|string|min:3|max:100',
            'email'=>'required|email|min:6|max:100',
            'direccion'=>'required|string|min:3|max:100',
            'telefono'=>'required|string|min:3|max:100'
        ]);

        $bool_proveedor = Proveedora::where('user_id',$user_loged->id)->where('id',$datos['id'])->exists();

        if(!$bool_proveedor){
            return response()->json(['error'=>403,'response'=>'Consulta equivocada'],403);
        }

        $proveedor = Proveedora::find($datos['id']);
        $proveedor->nombre = $datos['nombre'];
        $proveedor->contacto = $datos['contacto'];
        $proveedor->direccion = $datos['direccion'];
        $proveedor->telefono = $datos['telefono'];
        $proveedor->email = $datos['email'];
        $proveedor->save();

        return response()->json(['error'=>0,'proveedor'=>$proveedor],200);
    }

    public function delete(Request $request)
    {

        $user_loged = Auth()->User();

        $datos = $request->validate([
            'proveedor_id' => 'required|numeric|min:1'
        ]);

        $bool_proveedor = Proveedora::where('id',$datos['proveedor_id'])->where('user_id','=',$user_loged->id)->exists();

        if(!$bool_proveedor){
            return response()->json(['error'=>600,'response'=>'Proveedor no encontrado'],403);
        }

        $bool_entradas = Entrada::where('user_id',$user_loged->id)->where('proveedor_id',$datos['proveedor_id'])->exists();
        
        if($bool_entradas){
            return response()->json(['error'=>300,'response'=>'Proveedor con registros de entradas'],200);
        }

        $proveedor = Proveedora::find($datos['proveedor_id']);
        $proveedor->delete();
    
        return response()->json(['error'=>0,'response'=>'Proveedor eliminado'],200);

    }


}
