<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Proveedora;
use App\Models\Entrada;
use App\Models\Producto;
use App\Models\Salida;
use App\Models\Saldo;
use App\Models\Ubicacione;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendMail;
use stdClass;
use DateTime;

class UserController extends Controller
{

    public function index()
    {
        $user_loged = auth()->user();
       // $user_loged->last()

        return $this->get_data_inicial(['recurso'=>'index','user'=>$user_loged]);
    }

    public function index_reporte(Request $request)
    {

        $user_loged = auth()->user();

        $datos = $request->validate([
            'producto_id' => 'required|numeric|min:1',
            'ubicacion_id' => 'required|numeric|min:1'
        ]);

        $bool_producto = Producto::where('id',$datos['producto_id'])->where('user_id',$user_loged->id)->exists();
        $bool_ubicacion = Ubicacione::where('id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->exists();

        If(!$bool_producto || !$bool_ubicacion ){
            return response()->json(['error'=>403,'response'=>'Parametros incorrectos'],403);
        }

        $reporte = [];

        $entrada_default = new stdClass();
        $entrada_default->id = 0;
        $entrada_default->user_id = $user_loged->id;
        $entrada_default->proveedor_id = 0;
        $entrada_default->producto_id = 0;
        $entrada_default->ubicacion_id = 0;
        $entrada_default->costo_unidad = 0;
        $entrada_default->cantidad = 0;
        $entrada_default->pedido = '';
        $entrada_default->created_at =  new DateTime();;

        $entrada_default_g = new stdClass();
        $entrada_default_g->id = 0;
        $entrada_default_g->user_id = $user_loged->id;
        $entrada_default_g->proveedor_id = 0;
        $entrada_default_g->producto_id = 0;
        $entrada_default_g->ubicacion_id = 0;
        $entrada_default_g->costo_unidad = 0;
        $entrada_default_g->cantidad = 0;
        $entrada_default_g->pedido = '';
        $entrada_default_g->created_at =  new DateTime();;

        $salida_default = new stdClass();
        $salida_default->id = 0;
        $salida_default->user_id = $user_loged->id;
        $salida_default->ubicacion_id = 0;
        $salida_default->producto_id = 0;
        $salida_default->costo_unidad = 0;
        $salida_default->cantidad = 0;
        $salida_default->pedido = '';
        $salida_default->created_at =  new DateTime();;

        $bool_entrada = Entrada::where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->exists();

        if($bool_entrada){

            $entrada_reg = [];
            $entrada_reg = Entrada::where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->latest()->take(1)->get();

            $entrada = $entrada_reg[0];

        }else{

            $entrada = $entrada_default;
            
            $bool_entrada_g = Entrada::where('producto_id',$datos['producto_id'])->where('user_id',$user_loged->id)->exists();

            if($bool_entrada_g){

                $entrada_reg_g = Entrada::where('producto_id',$datos['producto_id'])->where('user_id',$user_loged->id)->latest()->take(1)->get();
                $entrada_default_g = $entrada_reg_g[0];

            }
            
        }


        $bool_salida = Salida::where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->exists();

        if($bool_salida){
        
            $salida_reg = [];
            $salida_reg = Salida::where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->latest()->take(1)->get();
        
            $salida = $salida_reg[0];

        }else{

            $salida = $salida_default;

        }

        $bool_saldo = Saldo::where('user_id',$user_loged->id)->where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->exists(); 
       
        $saldo =[];

        if(!$bool_saldo){

            $saldo = new Saldo();
            $saldo->user_id = $user_loged->id;
            $saldo->producto_id = $datos['producto_id'];
            $saldo->ubicacion_id = $datos['ubicacion_id'];
            $saldo->entradas = 0;
            $saldo->salidas = 0;
            $saldo->save();

        }else{

            $saldo = [];
            $saldo = Saldo::where('producto_id',$datos['producto_id'])->where('ubicacion_id',$datos['ubicacion_id'])->where('user_id',$user_loged->id)->first();
    
        }
        
        $entradas_global = 0;
        $entradas_global = Saldo::where('producto_id',$datos['producto_id'])->where('user_id',$user_loged->id)->sum('entradas');

        $salidas_global = 0;
        $salidas_global = Saldo::where('producto_id',$datos['producto_id'])->where('user_id',$user_loged->id)->sum('salidas');

        $proveedor = 'Proveedor';

        $bool_proveedor = Proveedora::where('user_id',$user_loged->id)->where('id',$entrada->proveedor_id)->exists();

        if($bool_proveedor){
            $proveedor_reg = Proveedora::where('user_id',$user_loged->id)->where('id',$entrada->proveedor_id)->first();
            $proveedor = $proveedor_reg->nombre; 
        }

        $reporte = new stdClass();
        //entrada
        $reporte->entrada = $entrada->cantidad ;
        $reporte->proveedor = $proveedor;
        $reporte->fecha_entrada = $entrada->created_at;
        $reporte->precio_entrada = $entrada->costo_unidad;
        
        //salida
        $reporte->salida = $salida->cantidad;
        $reporte->fecha_salida = $salida->created_at;
        $reporte->precio_salida = $salida->costo_unidad;
        
        //existencias
        $existencias = $saldo->entradas - $saldo->salidas;
        $existencias_global = $entradas_global - $salidas_global;

        $ubicacion = [];
        $ubicacion = Ubicacione::find($datos['ubicacion_id']);

        //utilidad
        $capital_global = 0;
        $utilidad_ubicacion = 0;
        $utilidad_global = 0;
     
        $producto = Producto::find($datos['producto_id']);
     
        if($entrada->costo_unidad > 0){
            
            $capital_global = $existencias_global * $entrada->costo_unidad; 
            $utilidad_ubicacion = (($entrada->costo_unidad/100)*$producto->margen_ganancia)*$existencias;
            $utilidad_global = (($entrada->costo_unidad/100)*$producto->margen_ganancia)*$existencias_global;
        
        }else{

            $capital_global = $existencias_global * $entrada_default_g->costo_unidad; ///
            $utilidad_ubicacion = (($entrada_default_g->costo_unidad/100)*$producto->margen_ganancia)*$existencias;
            $utilidad_global = (($entrada_default_g->costo_unidad/100)*$producto->margen_ganancia)*$existencias_global;

        }

        $reporte->total_entradas = $saldo->entradas;
        $reporte->total_salidas = $saldo->salidas;
        $reporte->ubicacion = $ubicacion->nombre;
        $reporte->existencias = $existencias;
        $reporte->capital_ubicacion= $existencias * $entrada->costo_unidad;
        $reporte->capital_global = $capital_global;
        $reporte->existencias_global = $existencias_global;
        $reporte->entradas_global = $entradas_global;
        $reporte->salidas_global = $salidas_global;
        $reporte->entrada_id = $entrada->id;
        $reporte->salida_id = $salida->id;
        $reporte->utilidad_ubicacion = $utilidad_ubicacion;
        $reporte->utilidad_global = $utilidad_global;
        $reporte->margen_ganancia = $producto->margen_ganancia;
        $reporte->id = 1;
        $reporte->producto_id = $producto->id;
        $reporte->ubicacion_id = $ubicacion->id;

        return response()->json(['error'=>0,'reporte'=>$reporte],200);

    }

    public function login(Request $request)
    {

        $array = $request->validate([
            'email' => 'required|string|email|max:256',
            'password' => 'required|string|min:6|max:12'
        ]);

        if (auth()->attempt( ['email'=>$array['email'],'password'=>$array['password']]) ){

            $user_loged = auth()->user();
            return $this->get_data_inicial(['recurso'=>'login','user'=>$user_loged]);

        }
        else
        {

            $bool_user = User::where('email','=',$request->email)->exists();
    
            if($bool_user){
                return response()->json(['error'=>300,'mensaje'=>'Wrong Passsword'],403);
            }
            else{
                return response()->json(['error'=>400,'mensaje'=>'User no found!'],403);
           
            }

        }

    }

    public function logout()
    { 
        
        auth()->user()->tokens()->delete();
        return response()->json(['error'=>0,'response'=>'Sesión finalizada'],200);
        
    }

    private function get_data_inicial($array){

        // recurso login,index

        $user_loged = $array['user'];
        $recurso = $array['recurso'];

        if($recurso == 'login'){
            $token = $user_loged->createToken('login')->plainTextToken;
            $user_loged['token']=$token;
        }

        return response()->json(['error'=>0,'user'=>$user_loged], 200);

    }

    public function registro(Request $request)
    {

        $datos = $request->validate([
            'email' => 'required|string|email|max:256|unique:users,email',
            'password' => 'required|string|min:6|max:20|confirmed',
            'password_confirmation' => 'required|string|min:6|max:20',
            'token'=>'required|string|min:20'
        ]);

        $res = Http::asForm()->post(config('app.g_cap_api'),['secret'=>config('app.g_cap_key'),'response'=> $datos['token']]);
        $data = json_decode($res);

        if($data->success == false || $data->score <= 0.6){
            return response()->json(['error' => 401,'response'=>'Validación captcha falló'],401);
        }

        $user_register = User::create([
            //'name' => strtoupper(strstr($request->email,'@',true)),
            //'alias' => strtoupper(strstr($request->email,'@',true)),
            'password' => bcrypt($datos['password']),
            'email' => $datos['email'],
            'avatar' => $this->getImageByFirstLetter($request->email)
        ]);

        $token = $user_register->createToken('register')->plainTextToken;

        $user_register['token_register']=$token;

        return response()->json(['error'=>0,'user'=>$user_register], 200);

    }

    public function restablecer(Request $request)
    {

        $datos = $request->validate([
            'email' => 'required|string|email|max:256',
            'token'=>'required|string|min:20'
        ]);

        $res = Http::asForm()->post(config('app.g_cap_api'),['secret'=>config('app.g_cap_key'),'response'=> $datos['token']]);
        $data = json_decode($res);

        if($data->success == false || $data->score <= 0.6){
            return response()->json(['error' => 401,'response'=>'Validación captcha falló'],401);
        }

        $bool_user = User::where('email','=',$datos['email'])->exists();

        if(!$bool_user){
            return response()->json(['error'=>400],200);
        }
        else
        {

            $user = User::where('email',$datos['email'])->first();

            $codigo = Str::random(20);
            $codigo_m = Str::upper($codigo);

            $u = User::find($user->id);
            $u->recovery = Hash::make($codigo_m);
            $u->save();

            $error = 0;

            try{

                $image = 'default.png';
                $image = $this->getImageByFirstLetter($user->email);

                $contenido = ['restablecer','Restablecer Contraseña',$user->email,$codigo,$image];
                SendMail::dispatch($contenido);
                //SendMail::dispatch($contenido)->onQueue('emails');

            }
            catch(\Exception $e){
                $error = 500;
                log::info($e);
            }

            return response()->json(['error'=>$error],200);

        }

    }

    public function recuperar(Request $request){

        $datos = $request->validate([
            'email' => 'required|string|email|max:256',
            'password'=> 'required|string|min:6|max:20|confirmed',
            'password_confirmation' => 'required|string|min:6|max:20',
            'key'=>'required|string|min:20'
        ]);
        
        $user_bool = User::where('email','=',$request->email)->exists();

        if($user_bool){

            $user = User::where('email','=',$request->email)->first();

            if(Hash::check(Str::upper($datos['key']), $user->recovery)){

                $u = User::find($user->id);
                $u->password = Hash::make($datos['password']);
                $u->recovery = '';
                $u->save();

                return response()->json(['error'=>0],200);

            }else{
                return response()->json(['error'=>400],200);
            }

        }else{

            return response()->json(['error'=>400],200);
        }

    }

    public function update_password(Request $request){

        $user_loged = auth()->user();

        $datos = $request->validate([
            'actual_password' => 'required|string|min:6|max:20',
            'password'=> 'required|string|min:6|max:20|confirmed',
            'password_confirmation' => 'required|string|min:6|max:20'
        ]);

        if(Hash::check($datos['actual_password'], $user_loged->password)){

            $u = User::find($user_loged->id);
            $u->password = Hash::make($datos['password']);
            $u->save();

            return response()->json(['error'=>0],200);

        }else{
            return response()->json(['error'=>300],200);
        }


    }

    private function getImageByFirstLetter($name){
        $nF = strtolower($name);
        $n = $nF[0].".png";    
        return $n;
    }
    
}
