<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Salida;
use App\Models\Saldo;
use App\Models\Producto;
use App\Models\Proveedora;
use App\Models\Ubicacione;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class SalidaController extends Controller
{
    private function date_time(){ 
        return Carbon::now('America/Bogota');
    }
    
    public function index(Request $request)
    {
        $user_loged = Auth()->User();

        $datos = $request->validate([
            'year'=>'required|numeric|min:0',
            'month'=>'required|numeric|min:0'
        ]);

        $salidas = [];

        if( intval($datos['year']) > 0){

            if( intval($datos['month']) > 0){
            
                $salidas = DB::table('salidas')
                ->join('productos', 'salidas.producto_id', '=', 'productos.id')
                ->join('ubicaciones', 'salidas.ubicacion_id', '=', 'ubicaciones.id')
                ->where('salidas.user_id',$user_loged->id)
                ->whereYear('salidas.created_at','=',date($datos['year']))
                ->whereMonth('salidas.created_at','=',date($datos['month']))
                ->select('productos.nombre as producto','productos.codigo','productos.marca','salidas.id','salidas.costo_unidad','salidas.cantidad','salidas.created_at','salidas.created_at as salida','salidas.pedido','ubicaciones.nombre as ubicacion')
                ->latest()->take(500)->orderBy('created_at','DESC')->get();
            
            }else{

                $salidas = DB::table('salidas')
                ->join('productos', 'salidas.producto_id', '=', 'productos.id')
                ->join('ubicaciones', 'salidas.ubicacion_id', '=', 'ubicaciones.id')
                ->where('salidas.user_id',$user_loged->id)
                ->whereYear('salidas.created_at','=',date($datos['year']))
                ->select('productos.nombre as producto','productos.codigo','productos.marca','salidas.id','salidas.costo_unidad','salidas.cantidad','salidas.created_at','salidas.created_at as salida','salidas.pedido','ubicaciones.nombre as ubicacion')
                ->latest()->take(500)->orderBy('created_at','DESC')->get();
            
            }

        }else{

            if( intval($datos['month']) > 0 ){
            
                $salidas = DB::table('salidas')
                ->join('productos', 'salidas.producto_id', '=', 'productos.id')
                ->join('ubicaciones', 'salidas.ubicacion_id', '=', 'ubicaciones.id')
                ->where('salidas.user_id',$user_loged->id)
                ->whereMonth('salidas.created_at','=',date($datos['month']))
                ->select('productos.nombre as producto','productos.codigo','productos.marca','salidas.id','salidas.costo_unidad','salidas.cantidad','salidas.created_at','salidas.created_at as salida','salidas.pedido','ubicaciones.nombre as ubicacion')
                ->latest()->take(500)->orderBy('created_at','DESC')->get();
            
            }else{

                $salidas = DB::table('salidas')
                ->join('productos', 'salidas.producto_id', '=', 'productos.id')
                ->join('ubicaciones', 'salidas.ubicacion_id', '=', 'ubicaciones.id')
                ->where('salidas.user_id',$user_loged->id)
                ->select('productos.nombre as producto','productos.codigo','productos.marca','salidas.id','salidas.costo_unidad','salidas.cantidad','salidas.created_at','salidas.created_at as salida','salidas.pedido','ubicaciones.nombre as ubicacion')
                ->latest()->take(500)->orderBy('created_at','DESC')->get();
            
            }

        }

        return response()->json(['error'=>0,'salidas'=>$salidas],200);

    }
    
    public function store(Request $request)
    {

        $user_loged = Auth()->User();
       // producto_id
        $datos = $request->validate([
            'producto_id'=>'required|numeric|min:1',
            'ubicacion_id'=>'required|numeric|min:1',
            'costo_unidad'=>'required|numeric|min:1|max:4294967295',
            'cantidad'=>'required|numeric|min:1|max:4294967295',
            'pedido'=>'required|string|min:1|max:100'
        ]);

        $bool_producto = Producto::where('id',$datos['producto_id'])->where('user_id',$user_loged->id)->exists();
        $bool_ubicacion = Ubicacione::where('id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->exists();
    
        if( !$bool_producto && !$bool_ubicacion){
            return response()->json(['error'=>403,'response'=>'Consulta equivocada'],403);
        }
        
        $bool_saldo = Saldo::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->exists(); 
       
        if($bool_saldo){

            $saldo = Saldo::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->first();
            
            $entradas = Saldo::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->sum('entradas');
            $salidas = Saldo::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->sum('salidas');

            if( ($entradas - $salidas) >=  intval($datos['cantidad'])){

                $saldo->salidas = $saldo->salidas + intval($datos['cantidad']);
                $saldo->save();
            
            }else{
                return response()->json(['error'=>300,'response'=>'No hay existencias suficientes en esta ubicación.'],200);
            }

        
        }else{

            return response()->json(['error'=>300,'response'=>'No hay existencias suficientes en esta ubicación.'],200);

        }

        $salida = Salida::create([
            'user_id'=>$user_loged->id,
            'producto_id'=>$datos['producto_id'],
            'ubicacion_id'=>$datos['ubicacion_id'],
            'costo_unidad'=>$datos['costo_unidad'],
            'cantidad'=>$datos['cantidad'],
            'pedido'=>$datos['pedido']
        ]);

        $producto = Producto::find($datos['producto_id']);
        $ubicacion = Ubicacione::find($datos['ubicacion_id']);

        $salida['producto'] = $producto->nombre;
        $salida['codigo'] = $producto->codigo;
        $salida['ubicacion'] = $ubicacion->nombre;
        $salida['salida'] = $this->date_time()->format('Y-m-d H:i:s');

        return response()->json(['error'=>0,'salida'=>$salida],200);

    }

    public function delete(Request $request)
    {
        $user_loged = Auth()->User();

        $datos = $request->validate([
            'salida_id'=>'required|numeric|min:1'
        ]);

        $bool_entrada = Salida::where('id',$datos['salida_id'])->where('user_id',$user_loged->id)->exists();

        if(!$bool_entrada){
            return response()->json(['error'=>300,'response'=>'La salida no existe'],200);
        }

        $salida = Salida::find($datos['salida_id']);

        $saldo = Saldo::where('user_id',$user_loged->id)->where('producto_id',$salida['producto_id'])->where('ubicacion_id',$salida['ubicacion_id'])->first();
        $saldo->salidas = $saldo->salidas - intval($salida['cantidad']);
        $saldo->save();

        $salida->delete();

        return response()->json(['error'=>0,'response'=>'Salida Eliminada'],200);
    }

}
