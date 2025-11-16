<?php

namespace Modules\Biblia\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BibliaController extends Controller
{
    /**
     * Lista todos os livros agrupados por testamento.
     */
    public function index()
    {
        $testamentos = DB::table('testaments')
            ->select('id', 'name')
            ->get()
            ->map(function ($testamento) {
                $testamento->books = DB::table('books')
                    ->where('testament_reference_id', $testamento->id)
                    ->orderBy('id')
                    ->get();
                return $testamento;
            });

        return view('biblia::index', compact('testamentos'));
    }

    /**
     * Lista capítulos de um livro específico.
     */
    public function chapters($bookId)
    {
        $capitulos = DB::table('verses')
            ->where('book_id', $bookId)
            ->select('chapter')
            ->distinct()
            ->orderBy('chapter')
            ->pluck('chapter');

        $book = DB::table('books')->where('id', $bookId)->first();

        return view('biblia::chapters', compact('capitulos', 'book'));
    }

    /**
     * Lista versículos de um capítulo.
     */
    public function verses($bookId, $chapter)
    {
        $versiculos = DB::table('verses')
            ->where('book_id', $bookId)
            ->where('chapter', $chapter)
            ->orderBy('verse')
            ->get();

        $book = DB::table('books')->where('id', $bookId)->first();
        
        // Buscar o número máximo de capítulos do livro
        $maxChapter = DB::table('verses')
            ->where('book_id', $bookId)
            ->max('chapter');

        return view('biblia::verses', compact('versiculos', 'book', 'chapter', 'maxChapter'));
    }

    /**
     * Busca texto na Bíblia inteira.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        $resultados = DB::table('verses')
            ->join('books', 'books.id', '=', 'verses.book_id')
            ->where('verses.text', 'LIKE', '%' . $query . '%')
            ->select('books.name as book', 'books.id as idBook', 'verses.chapter', 'verses.verse', 'verses.text', 'verses.id')
            ->orderBy('books.id')
            ->orderBy('verses.chapter')
            ->orderBy('verses.verse')
            ->get();

        return view('biblia::search', compact('resultados', 'query'));
    }
}
