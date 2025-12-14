<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CultoCategoria;
use Illuminate\Http\Request;

class CultoCategoriaController extends Controller
{
    public function index()
    {
        $categorias = CultoCategoria::orderBy('nome')->get();
        return view('cultos.includes.categorias_modal', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:255',
        ]);

        CultoCategoria::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);

        return redirect()->back()->with('msg', 'Categoria adicionada com sucesso.');
    }

    public function destroy($id)
    {
        $categoria = CultoCategoria::findOrFail($id);
        $categoria->delete();

        return redirect()->back()->with('msg', 'Categoria removida com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:255',
        ]);

        $categoria = CultoCategoria::findOrFail($id);
        $categoria->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);

        return redirect()->back()->with('msg', 'Categoria atualizada com sucesso.');
    }
}
