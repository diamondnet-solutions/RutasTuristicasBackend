<?php

namespace App\Http\Controllers;

use App\Models\LugarTuristico;
use Illuminate\Http\Request;

class LugarTuristicoController extends Controller
{

    public function index()
    {
        return response()->json(LugarTuristico::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'ubicacion' => 'required|string',
            'imagen' => 'nullable|string',
        ]);

        $lugar = LugarTuristico::create($data);
        return response()->json($lugar, 201);
    }

    public function show($id)
    {
        $lugar = LugarTuristico::findOrFail($id);
        return response()->json($lugar);
    }

    public function update(Request $request, $id)
    {
        $lugar = LugarTuristico::findOrFail($id);
        $lugar->update($request->only(['nombre', 'descripcion', 'ubicacion', 'imagen']));
        return response()->json($lugar);
    }

    public function destroy($id)
    {
        $lugar = LugarTuristico::findOrFail($id);
        $lugar->delete();
        return response()->json(['message' => 'Lugar eliminado correctamente']);
    }
}
