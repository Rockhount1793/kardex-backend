<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Saldo;
use App\Models\Producto;
use App\Models\Proveedora;
use App\Models\Ubicacione;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class EntradaController extends Controller
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

        $entradas = [];

        if( intval($datos['year']) > 0){

            if( intval($datos['month']) > 0){
            
                $entradas = DB::table('entradas')
                ->join('productos', 'entradas.producto_id', '=', 'productos.id')
                ->join('proveedoras', 'entradas.proveedor_id', '=', 'proveedoras.id')
                ->join('ubicaciones', 'entradas.ubicacion_id', '=', 'ubicaciones.id')
                ->where('entradas.user_id',$user_loged->id)
                ->whereYear('entradas.created_at','=',date($datos['year']))
                ->whereMonth('entradas.created_at','=',date($datos['month']))
                ->select('productos.nombre as producto','productos.codigo','productos.marca','entradas.id','entradas.costo_unidad','entradas.cantidad','entradas.created_at','entradas.created_at as entrada','entradas.pedido','proveedoras.nombre as proveedor','ubicaciones.nombre as ubicacion')
                ->latest()->take(500)->orderBy('created_at','DESC')->get();
            
            }else{

                $entradas = DB::table('entradas')
                ->join('productos', 'entradas.producto_id', '=', 'productos.id')
                ->join('proveedoras', 'entradas.proveedor_id', '=', 'proveedoras.id')
                ->join('ubicaciones', 'entradas.ubicacion_id', '=', 'ubicaciones.id')
                ->where('entradas.user_id',$user_loged->id)
                ->whereYear('entradas.created_at','=',date($datos['year']))
                ->select('productos.nombre as producto','productos.codigo','productos.marca','entradas.id','entradas.costo_unidad','entradas.cantidad','entradas.created_at','entradas.created_at as entrada','entradas.pedido','proveedoras.nombre as proveedor','ubicaciones.nombre as ubicacion')
                ->latest()->take(500)->orderBy('created_at','DESC')->get();
            
            }

        }else{

            if( intval($datos['month']) > 0 ){
            
                $entradas = DB::table('entradas')
                ->join('productos', 'entradas.producto_id', '=', 'productos.id')
                ->join('proveedoras', 'entradas.proveedor_id', '=', 'proveedoras.id')
                ->join('ubicaciones', 'entradas.ubicacion_id', '=', 'ubicaciones.id')
                ->where('entradas.user_id',$user_loged->id)
                ->whereMonth('entradas.created_at','=',date($datos['month']))
                ->select('productos.nombre as producto','productos.codigo','productos.marca','entradas.id','entradas.costo_unidad','entradas.cantidad','entradas.created_at','entradas.created_at as entrada','entradas.pedido','proveedoras.nombre as proveedor','ubicaciones.nombre as ubicacion')
                ->latest()->take(500)->orderBy('created_at','DESC')->get();
            
            }else{

                $entradas = DB::table('entradas')
                ->join('productos', 'entradas.producto_id', '=', 'productos.id')
                ->join('proveedoras', 'entradas.proveedor_id', '=', 'proveedoras.id')
                ->join('ubicaciones', 'entradas.ubicacion_id', '=', 'ubicaciones.id')
                ->where('entradas.user_id',$user_loged->id)
                ->select('productos.nombre as producto','productos.codigo','productos.marca','entradas.id','entradas.costo_unidad','entradas.cantidad','entradas.created_at','entradas.created_at as entrada','entradas.pedido','proveedoras.nombre as proveedor','ubicaciones.nombre as ubicacion')
                ->latest()->take(500)->orderBy('created_at','DESC')->get();
            
            }

        }

        return response()->json(['error'=>0,'entradas'=>$entradas],200);

    }

    public function store(Request $request)
    {
        $user_loged = Auth()->User();

        $datos = $request->validate([
            'producto_id'=>'required|numeric|min:1',
            'proveedor_id'=>'required|numeric|min:1',
            'ubicacion_id'=>'required|numeric|min:1',
            'costo_unidad'=>'required|numeric|min:1|max:4294967295',
            'cantidad'=>'required|numeric|min:1|max:4294967295',
            'pedido'=>'required|string|min:1|max:100'
        ]);

        $bool_producto = Producto::where('id',$datos['producto_id'])->where('user_id',$user_loged->id)->exists();
        $bool_ubicacion = Ubicacione::where('id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->exists();
        $bool_proveedor = Proveedora::where('id',$datos['proveedor_id'])->where('user_id',$user_loged->id)->exists();

        if( !$bool_producto && !$bool_ubicacion && !$bool_proveedor){
            return response()->json(['error'=>403,'response'=>'Consulta equivocada'],403);
        }

        $entrada = Entrada::create([
            'user_id'=>$user_loged->id,
            'producto_id'=>$datos['producto_id'],
            'proveedor_id'=>$datos['proveedor_id'],
            'ubicacion_id'=>$datos['ubicacion_id'],
            'costo_unidad'=>$datos['costo_unidad'],
            'cantidad'=>$datos['cantidad'],
            'pedido'=>$datos['pedido'],
        ]);

        $producto = Producto::find($datos['producto_id']);
        $ubicacion = Ubicacione::find($datos['ubicacion_id']);
        $proveedor = Proveedora::find($datos['proveedor_id']);

        $bool_saldo = Saldo::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->exists(); 
       
        if($bool_saldo){

            $saldo = Saldo::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->first();
            $saldo->entradas = $saldo->entradas + intval($datos['cantidad']);
            $saldo->save();
        
        }else{

            $saldo = new Saldo();
            $saldo->user_id = $user_loged->id;
            $saldo->producto_id = $datos['producto_id'];
            $saldo->ubicacion_id = $datos['ubicacion_id'];
            $saldo->entradas = intval($datos['cantidad']);
            $saldo->salidas = 0;
            $saldo->save();
        }

        $entrada['producto'] = $producto->nombre;
        $entrada['codigo'] = $producto->codigo;
        $entrada['ubicacion'] = $ubicacion->nombre;
        $entrada['proveedor'] = $proveedor->nombre;
        $entrada['entrada'] = $this->date_time()->format('Y-m-d H:i:s');

        return response()->json(['error'=>0,'entrada'=>$entrada],200);

    }

    public function delete(Request $request)
    {
        $user_loged = Auth()->User();

        $datos = $request->validate([
            'entrada_id'=>'required|numeric|min:1'
        ]);

        $bool_entrada = Entrada::where('id',$datos['entrada_id'])->where('user_id',$user_loged->id)->exists();

        if(!$bool_entrada){
            return response()->json(['error'=>300,'response'=>'La entrada no existe'],200);
        }

        $entrada = Entrada::find($datos['entrada_id']);

        $saldo = Saldo::where('user_id',$user_loged->id)->where('producto_id',$entrada['producto_id'])->where('ubicacion_id',$entrada['ubicacion_id'])->first();
        $saldo->entradas = $saldo->entradas - intval($entrada['cantidad']);
        $saldo->save();

        $entrada->delete();

        return response()->json(['error'=>0,'response'=>'Entrada Eliminada'],200);
    }

    public function latest(Request $request){

        $user_loged = Auth()->User();

        $datos = $request->validate([
            'producto_id'=>'required|numeric|min:1',
            'ubicacion_id'=>'required|numeric|min:1'
        ]);

        $entrada = [];
        $entrada = Entrada::where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->latest()->take(1)->get();

        if(count($entrada) == 1){

            foreach ($entrada as $key => $item) {
                
                $proveedor = Proveedora::find($item->proveedor_id);
                $saldo = Saldo::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->first();
                $producto = Producto::find($datos['producto_id']);

                $item['proveedor'] = $proveedor->nombre;
                $item['entradas'] = $saldo->entradas;
                $item['salidas'] = $saldo->salidas;
                $item['margen_ganancia'] = $producto->margen_ganancia;
                $item['precio_sugerido'] = (($item->costo_unidad/100) * $producto->margen_ganancia) + $item->costo_unidad;
            }

        }

        return response()->json(['error'=>0,'ultima_entrada'=>$entrada],200);
    }
}