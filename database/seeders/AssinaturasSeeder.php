<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class AssinaturasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Date::now();

        $tipos = [
            'Revista Digital',
            'Jornal Semanal',
            'Podcast Premium',
            'Devocional Diário',
            'Combo Físico',
        ];

        foreach ($tipos as $nomeTipo) {
            DB::table('tipo_produto')->updateOrInsert(
                ['nome' => $nomeTipo],
                ['atualizado_em' => $now]
            );
        }

        $tipoIds = DB::table('tipo_produto')
            ->whereIn('nome', $tipos)
            ->pluck('id', 'nome')
            ->toArray();

        $produtos = [
            [
                'titulo' => 'Revista Jovem Esperança',
                'descricao' => 'Conteúdo inspirador mensal com devocionais, testemunhos e planos de estudo bíblico.',
                'preco' => 19.90,
                'data_lancamento' => '2024-02-01',
                'capa_url' => 'https://example.com/imagens/revista-jovem.jpg',
                'arquivo_url' => 'https://example.com/downloads/revista-jovem.pdf',
                'tipo' => 'Revista Digital',
            ],
            [
                'titulo' => 'Jornal Comunidade Viva',
                'descricao' => 'Resumo semanal das atividades, notícias e agenda da congregação.',
                'preco' => 9.90,
                'data_lancamento' => '2024-03-15',
                'capa_url' => 'https://example.com/imagens/jornal-comunidade.jpg',
                'arquivo_url' => null,
                'tipo' => 'Jornal Semanal',
            ],
            [
                'titulo' => 'Podcast Palavra em Foco',
                'descricao' => 'Série de podcasts exclusivos com entrevistas, estudos e bate-papos sobre fé.',
                'preco' => 24.90,
                'data_lancamento' => '2024-04-10',
                'capa_url' => 'https://example.com/imagens/podcast-palavra.jpg',
                'arquivo_url' => 'https://example.com/podcasts/palavra-em-foco.m3u8',
                'tipo' => 'Podcast Premium',
            ],
            [
                'titulo' => 'Devocional Café com Fé',
                'descricao' => 'Plano diário com reflexões rápidas para iniciar o dia conectado com Deus.',
                'preco' => 14.90,
                'data_lancamento' => '2024-01-05',
                'capa_url' => 'https://example.com/imagens/devocional-cafe.jpg',
                'arquivo_url' => 'https://example.com/app/devocional-cafe.epub',
                'tipo' => 'Devocional Diário',
            ],
            [
                'titulo' => 'Combo Família em Ação',
                'descricao' => 'Caixa mensal com materiais físicos, guias de estudo e atividades para toda a família.',
                'preco' => 89.90,
                'data_lancamento' => '2024-05-20',
                'capa_url' => 'https://example.com/imagens/combo-familia.jpg',
                'arquivo_url' => null,
                'tipo' => 'Combo Físico',
            ],
        ];

        foreach ($produtos as $produto) {
            DB::table('produtos_assinatura')->updateOrInsert(
                ['titulo' => $produto['titulo']],
                [
                    'congregacao_id' => null,
                    'tipo_id' => $tipoIds[$produto['tipo']] ?? null,
                    'descricao' => $produto['descricao'],
                    'preco' => $produto['preco'],
                    'ativo' => true,
                    'data_lancamento' => $produto['data_lancamento'],
                    'capa_url' => $produto['capa_url'],
                    'arquivo_url' => $produto['arquivo_url'],
                    'atualizado_em' => $now,
                ]
            );
        }

        $planos = [
            [
                'nome' => 'Plano Essencial',
                'descricao' => 'Acesso às publicações digitais principais com renovação mensal.',
                'periodicidade' => 'mensal',
                'valor' => 29.90,
            ],
            [
                'nome' => 'Plano Comunidade',
                'descricao' => 'Inclui materiais digitais, podcasts exclusivos e encontros trimestrais.',
                'periodicidade' => 'trimestral',
                'valor' => 79.90,
            ],
            [
                'nome' => 'Plano Família',
                'descricao' => 'Experiência completa com kits físicos, podcasts e recursos para famílias.',
                'periodicidade' => 'anual',
                'valor' => 799.00,
            ],
        ];

        foreach ($planos as $plano) {
            DB::table('planos_assinatura')->updateOrInsert(
                ['nome' => $plano['nome']],
                [
                    'congregacao_id' => 1,
                    'descricao' => $plano['descricao'],
                    'periodicidade' => $plano['periodicidade'],
                    'valor' => $plano['valor'],
                    'ativo' => true,
                    'atualizado_em' => $now,
                ]
            );
        }
    }
}
