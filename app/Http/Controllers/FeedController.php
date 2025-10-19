<?php 

namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Support\Str;

class FeedController extends Controller
{
    // Lista todas as notícias
    public function noticias()
    {
        $noticias = $this->groupedFeedsByFonte('noticia', 15);

        return view('noticias.painel', compact('noticias'));
    }

    public function podcasts()
    {
        $podcasts = $this->groupedFeedsByFonte('podcast', 9);

        return view('podcasts.painel', compact('podcasts'));
    }

    // Destaques (ex: só fonte "guiame")
    public function destaques()
    {
        $destaques = Feed::query()
            ->where('categoria', 'noticia')
            ->where('fonte', 'guiame')
            ->orderByDesc('publicado_em')
            ->orderByDesc('created_at')
            ->limit(9)
            ->get()
            ->map(fn (Feed $feed) => $this->formatFeed($feed))
            ->all();

        return view('noticias.includes.destaques', compact('destaques'));
    }

    /**
     * Agrupa feeds por fonte, limitando quantidade e montando dados para exibição.
     */
    protected function groupedFeedsByFonte(string $categoria, int $limitPerFonte): array
    {
        return Feed::query()
            ->where('categoria', $categoria)
            ->orderByDesc('publicado_em')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn (Feed $feed) => $feed->fonte ?: 'Outros')
            ->map(function ($items) use ($limitPerFonte) {
                return $items
                    ->take($limitPerFonte)
                    ->map(fn (Feed $feed) => $this->formatFeed($feed))
                    ->values();
            })
            ->filter(fn ($collection) => $collection->isNotEmpty())
            ->sortKeys()
            ->toArray();
    }

    /**
     * Formata um registro de feed com os campos principais utilizados nas views.
     */
    protected function formatFeed(Feed $feed): array
    {
        $resumoBase = $feed->descricao ?? $feed->conteudo ?? '';

        return [
            'id' => $feed->id,
            'titulo' => $feed->titulo,
            'resumo' => Str::limit(trim(strip_tags($resumoBase)), 240),
            'descricao' => $feed->descricao,
            'conteudo' => $feed->conteudo,
            'link' => $feed->link,
            'fonte' => $feed->fonte ?: 'Outros',
            'categoria' => $feed->categoria,
            'imagem' => $feed->imagem_capa ?: $feed->media_url,
            'imagem_capa' => $feed->imagem_capa,
            'media_url' => $feed->media_url,
            'has_audio' => $this->isAudio($feed->media_url),
            'publicado_em' => optional($feed->publicado_em)->format('d/m/Y H:i'),
            'publicado_em_iso' => optional($feed->publicado_em)->toIso8601String(),
        ];
    }

    /**
     * Verifica se a URL aponta para um arquivo de áudio.
     */
    protected function isAudio(?string $url): bool
    {
        if (blank($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $extensao = Str::lower(Str::afterLast($path, '.'));

        return in_array($extensao, ['mp3', 'm4a', 'aac', 'wav', 'ogg', 'oga', 'mpga'], true);
    }
}
?>
