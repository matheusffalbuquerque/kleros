@extends('layouts.main')

@section('title', 'Notícias | ' . $appName)

@section('content')

<div class="container">
    <h1>Notícias Cristãs</h1>
    <div class="info">
        <h3>Cristianismo pelo mundo</h3>
        <div class="noticias-container">
            @forelse ($noticias as $fonte => $lista)
                <h4 class="noticias-fonte">{{ $fonte }}</h4>
                <div class="noticias-grid">
                    @foreach ($lista as $noticia)
                        <div class="noticia-card">
                            @if (!empty($noticia['imagem']))
                                <img src="{{ $noticia['imagem'] }}" alt="{{ $noticia['titulo'] }}" class="noticia-img">
                            @endif
                            <a href="{{ $noticia['link'] }}" target="_blank" class="noticia-title">
                                {{ $noticia['titulo'] }}
                            </a>
                            <div class="noticia-date" title="{{ $noticia['publicado_em_iso'] }}">
                                {{ $noticia['publicado_em'] }}
                            </div>
                            <div class="noticia-desc">
                                {{ $noticia['resumo'] }}
                            </div>
                            <a href="{{ $noticia['link'] }}" target="_blank" rel="noopener noreferrer" class="noticia-link" onclick='abrirJanelaModal(@json($noticia['link']), { iframe: true, title: @json($noticia['titulo'] ?? "Notícia") }); return false;'>
                                Ler mais
                            </a>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="noticias-grid">
                    <p class="text-center text-white/60">Nenhuma notícia encontrada no momento.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
