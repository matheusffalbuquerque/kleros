<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Feed;
use Carbon\Carbon;

class AtualizarFeedsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $feeds = [
            'btcast' => 'https://bibotalk.com/feed/',
            'guiame' => 'https://guiame.com.br/rss',
        ];

        foreach ($feeds as $nome => $url) {
            try {
                $xml = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);

                foreach ($xml->channel->item as $item) {
                    $titulo = (string) $item->title;
                    $link = (string) $item->link;
                    $descricao = (string) ($item->description ?? null);
                    $conteudo = (string) ($item->children('content', true)->encoded ?? $descricao);

                    $pubDate = isset($item->pubDate) ? Carbon::parse($item->pubDate) : now();
                    $slug = Str::slug($titulo.'-'.md5($link));

                    // 🔑 categoria
                    $categoria = in_array($nome, ['btcast','cafecf']) ? 'podcast' : 'noticia';

                    // 🔑 áudio
                    $audio = null;

                    // enclosure padrão (geral)
                    if (isset($item->enclosure) && $item->enclosure['url']) {
                        $audioPull = (string) $item->enclosure['url'];
                        $audio = strtok($audioPull, '?');
                    }

                    // media:content (alguns feeds usam isso)
                    if (!$audio) {
                        $media = $item->children('http://search.yahoo.com/mrss/');
                        if ($media && $media->content && $media->content->attributes()->url) {
                            $audioPull = (string) $media->content->attributes()->url;
                            $audio = strtok($audioPull, '?');
                        }
                    }

                    // itunes: (alguns usam dentro do namespace itunes como <enclosure>)
                    if (!$audio) {
                        $itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
                        if ($itunes && $itunes->attributes()->href) {
                            $audioPull = (string) $itunes->attributes()->href;
                            $audio = strtok($audioPull, '?');
                        }
                    }

                    // 🔑 imagem
                    $imagem = null;
                    $itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
                    $media  = $item->children('http://search.yahoo.com/mrss/');

                    if ($itunes && $itunes->image && $itunes->image->attributes()->href) {
                        $imagem = (string) $itunes->image->attributes()->href;
                    } elseif ($media && $media->thumbnail && $media->thumbnail->attributes()->url) {
                        $imagem = (string) $media->thumbnail->attributes()->url;
                    } elseif (isset($xml->channel->image->url)) {
                        $imagem = (string) $xml->channel->image->url;
                    }

                    // monta os dados principais
                    $dados = [
                        'titulo'       => html_entity_decode($titulo, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                        'link'         => $link,
                        'descricao'    => html_entity_decode(strip_tags($descricao), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                        'conteudo'     => html_entity_decode($conteudo, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                        'imagem_capa'  => $imagem,
                        'fonte'        => $nome,
                        'tipo'         => 'rss',
                        'categoria'    => $categoria,
                        'publicado_em' => $pubDate,
                    ];

                    // só atualiza media_url se tiver um áudio válido
                    if (!empty($audio)) {
                        $dados['media_url'] = $audio;
                    }

                    $feed = Feed::updateOrCreate(
                        ['slug' => $slug],
                        $dados
                    );
                }
            } catch (\Exception $e) {
                Log::error("Erro ao atualizar feed {$nome}: ".$e->getMessage());
            }
        }
    }
}