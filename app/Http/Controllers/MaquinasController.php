<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use Illuminate\Http\Request;

class MaquinasController extends Controller
{
    function index() {
        $maquinas = Maquina::get();
        return response()->json($maquinas);
    }

    public function update(Request $request, $id)
    {
        $material = Maquina::findOrFail($id);

        $validated = $request->validate([
            'vida_util_horas' => 'required|numeric',
            'valor_maquina' => 'required|numeric',
        ]);

        $material->update($validated);
            
        return response()->json(['message' => 'Maquina atualizado com sucesso!']);
    }
}