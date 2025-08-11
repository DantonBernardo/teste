<?php

namespace App\Http\Controllers;

use App\Models\Materiais;
use Illuminate\Http\Request;

class MateriaisController extends Controller
{
    function index() {
        $materiais = Materiais::get();
        return response()->json($materiais);
    }

    public function update(Request $request, $id)
    {
        $material = Materiais::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string',
            'preco_por_kg' => 'required|numeric',
        ]);

        $material->update($validated);

        return response()->json(['message' => 'Material atualizado com sucesso!']);
    }
}
