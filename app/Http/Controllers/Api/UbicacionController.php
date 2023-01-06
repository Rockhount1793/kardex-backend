<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ubicacione;
use App\Models\Entrada;
use App\Models\Salida;
use Illuminate\Support\Facades\Log;
use App\Models\Saldo;


class UbicacionController extends Controller
{

    public function index()
    {
        $user_loged = Auth()->User();
        
        $ubicaciones = [];
        $ubicaciones = Ubicacione::where('user_id',$user_loged->id)->take(500)->orderBy('created_at','DESC')->get();

        return response()->json(['error'=>0,'ubicaciones'=>$ubicaciones],200);
    }

    public function store(Request $request)
    {
        $user_loged = Auth()->User();

        $datos = $request->validate([
            'nombre'=>'required|string|min:5|max:100',
            'direccion'=>'required|string|min:3|max:100',
            'telefono'=>'required|string|min:3|max:100'
        ]);

        $count_ubicaciones = 0;
        $count_ubicaciones = Ubicacione::where('user_id',$user_loged->id)->count();

        if($count_ubicaciones >= 500){
            return response()->json(['error'=>300,'response'=>'Máximo 500 ubicaciones'],200);
        }

        $ubicacion = Ubicacione::create([
            'user_id'=>$user_loged->id,
            'administrador_id'=>$user_loged->id,
            'nombre'=>$datos['nombre'],
            'direccion'=>$datos['direccion'],
            'telefono'=>$datos['telefono']
        ]);

        return response()->json(['error'=>0,'ubicacion'=>$ubicacion],200);

    }

    public function update(Request $request)
    {
        $user_loged = Auth()->User();
        
        $datos = $request->validate([
            'id'=>'required|numeric|min:1',
            'nombre'=>'required|string|min:3|max:100',
            'direccion'=>'required|string|min:3|max:100',
            'telefono'=>'required|string|min:3|max:100'
        ]);

        $bool_ubicacion = Ubicacione::where('user_id',$user_loged->id)->where('id',$datos['id'])->exists();

        if(!$bool_ubicacion){
            return response()->json(['error'=>403,'response'=>'Consulta equivocada'],403);
        }

        $ubicacion = Ubicacione::find($datos['id']);
        $ubicacion->nombre = $datos['nombre'];
        $ubicacion->direccion = $datos['direccion'];
        $ubicacion->telefono = $datos['telefono'];
        $ubicacion->save();

        return response()->json(['error'=>0,'ubicacion'=>$ubicacion],200);
    }

    public function delete(Request $request){

        $user_loged = Auth()->User();
        
        $datos = $request->validate([
            'ubicacion_id'=>'required|numeric|min:1'
        ]);

        $bool_ubicacion = Ubicacione::where('user_id',$user_loged->id)->where('id',$datos['ubicacion_id'])->exists();
        
        $bool_entradas = Entrada::where('user_id',$user_loged->id)->where('ubicacion_id',$datos['ubicacion_id'])->exists();
        $bool_salidas = Salida::where('user_id',$user_loged->id)->where('ubicacion_id',$datos['ubicacion_id'])->exists();

        if(!$bool_ubicacion){
            return response()->json(['error'=>403,'response'=>'Ubicación no existe'],403);
        }

        if($bool_entradas){
            return response()->json(['error'=>300,'response'=>'Ubicación con registros de entradas'],200);
        }
        
        if($bool_salidas){
            return response()->json(['error'=>400,'response'=>'Ubicación con registros de salida'],200);
        }


        $bool_saldo = Saldo::where('user_id',$user_loged->id)->where('ubicacion_id',$datos['ubicacion_id'])->exists(); 
       
        if($bool_saldo){

            $saldo = Saldo::where('user_id',$user_loged->id)->where('ubicacion_id',$datos['ubicacion_id'])->delete();
            
        }

        $ubicacion = Ubicacione::where('id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->delete();
        
        return response()->json(['error'=>0,'response'=>'Ubicacion eliminada'],200);
    }

}
