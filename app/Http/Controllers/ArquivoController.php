<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arquivo;
use Illuminate\Support\Facades\Storage;

class ArquivoController extends Controller
{
    public function form_imagens()
    {
        $arquivos = Arquivo::where('congregacao_id', app('congregacao')->id)->where('tipo', 'imagem')->get();

        return view('arquivos.includes.form_imagens', ['arquivos' => $arquivos]);
    }

    public function gestorImagensLivewire()
    {
        // Retorna apenas o HTML do componente Livewire, sem layout
        return view('components.livewire-modal-wrapper', [
            'component' => 'gestor-imagens'
        ]);
    }

    public function store(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $path = $file->store('congregacoes/' . app('congregacao')->id . '/imagens', 'public');

            $arquivo = new Arquivo();
            $arquivo->nome = $file->getClientOriginalName();
            $arquivo->caminho = $path;
            $arquivo->tipo = 'imagem';
            $arquivo->congregacao_id = app('congregacao')->id;
            $arquivo->save();
        }

        return back()->with('success', 'Arquivo enviado com sucesso!');
    }

    public function destroy($id)
    {
        $arquivo = Arquivo::findOrFail($id);
        // Verifica se o arquivo pertence à congregação atual
        if ($arquivo->congregacao_id != app('congregacao')->id) {
            return back()->with('error', 'Ação não autorizada.');
        }

        // Deleta o arquivo do sistema de arquivos
        Storage::disk('public')->delete($arquivo->caminho);

        // Deleta o registro do banco de dados
        $arquivo->delete();

        return back()->with('success', 'Arquivo excluído com sucesso!');
    }

    public function lista_imagens()
    {
        $arquivos = Arquivo::where('congregacao_id', app('congregacao')->id)->where('tipo', 'imagem')->get();
        return view('arquivos.partials.lista_imagens', ['arquivos' => $arquivos]);
    }
}
