<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CategoriaController extends Controller
{
    
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $user = Auth()->User();

        $datos = $request->validate([
            'nombre' => 'required|string|min:2|max:100',
        ]);

        $cat = Categoria::create([
            'nombre' => $datos['nombre'],
            'color' => 'F0F0F0',
            'user_id' => $user->id,
        ]);

        return response()->json(['error'=>0,'categoria'=>$cat], 200);
    }
    
    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }
    
    public function destroy($id)
    {
        //
    }

}