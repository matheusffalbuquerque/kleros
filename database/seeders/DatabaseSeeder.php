<?php

namespace Database\Seeders;

use App\Models\Aviso;
use App\Models\Congregacao;
use App\Models\Extensao;
use App\Models\Feed;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DadosFixosSeeder::class,
        ]);

        $congregacao1 = Congregacao::find(1);
        $congregacao2 = Congregacao::find(2);

        if ($congregacao1) {
            Feed::create([
                'congregacao_id' => $congregacao1->id,
                'titulo' => 'Encontro especial de liderança',
                'slug' => Str::slug('Encontro especial de liderança-' . $congregacao1->id),
                'descricao' => 'Momento de alinhamento e oração para todos os líderes da congregação.',
                'conteudo' => 'O encontro acontecerá no auditório principal às 19h. Teremos um tempo de partilha e planejamento.',
                'fonte' => 'Equipe pastoral',
                'tipo' => 'manual',
                'categoria' => 'noticia',
                'publicado_em' => now()->subDays(2),
            ]);

            Feed::create([
                'congregacao_id' => $congregacao1->id,
                'titulo' => 'Podcast semanal - Palavra Viva',
                'slug' => Str::slug('Podcast Palavra Viva-' . $congregacao1->id),
                'descricao' => 'Na edição desta semana falamos sobre esperança em tempos de crise.',
                'conteudo' => null,
                'fonte' => 'Ministério de mídia',
                'tipo' => 'manual',
                'categoria' => 'podcast',
                'media_url' => 'https://example.com/podcasts/palavra-viva.mp3',
                'publicado_em' => now()->subDay(),
            ]);

            Aviso::create([
                'congregacao_id' => $congregacao1->id,
                'titulo' => 'Escala de oração',
                'mensagem' => 'Participe da nossa escala diária de oração às 6h da manhã.',
                'para_todos' => true,
                'data_inicio' => now()->subDay(),
                'status' => 'ativo',
                'prioridade' => 'normal',
            ]);

            Livro::create([
                'congregacao_id' => $congregacao1->id,
                'titulo' => 'Vida com Propósito',
                'autor' => 'Rick Warren',
                'capa' => 'https://images-na.ssl-images-amazon.com/images/I/81F4mwQFJ3L.jpg',
                'link' => 'https://example.com/livros/vida-com-proposito',
                'descricao' => 'Guia de 40 dias para uma jornada espiritual transformadora.',
            ]);

            Extensao::updateOrCreate(
                ['congregacao_id' => $congregacao1->id, 'module' => 'noticias'],
                ['enabled' => true]
            );
            Extensao::updateOrCreate(
                ['congregacao_id' => $congregacao1->id, 'module' => 'podcasts'],
                ['enabled' => true]
            );
            Extensao::updateOrCreate(
                ['congregacao_id' => $congregacao1->id, 'module' => 'mensagens'],
                ['enabled' => true]
            );
            Extensao::updateOrCreate(
                ['congregacao_id' => $congregacao1->id, 'module' => 'livraria'],
                ['enabled' => true]
            );
        }

        if ($congregacao2) {
            Feed::create([
                'congregacao_id' => $congregacao2->id,
                'titulo' => 'Notícias da semana',
                'slug' => Str::slug('Notícias da semana-' . $congregacao2->id),
                'descricao' => 'Resumo das principais atividades que aconteceram na comunidade.',
                'fonte' => 'Comunicação Ágape',
                'tipo' => 'manual',
                'categoria' => 'noticia',
                'publicado_em' => now()->subDays(3),
            ]);

            Extensao::updateOrCreate(
                ['congregacao_id' => $congregacao2->id, 'module' => 'noticias'],
                ['enabled' => true]
            );
        }
    }
}
