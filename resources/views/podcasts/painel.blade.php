@extends('layouts.main')

@section('title', 'Podcasts | ' . $appName)

@section('content')

<div class="container">
    <h1>Podcasts</h1>
    <div class="info">
        <h3 id="teologia">Aprender ouvindo</h3>
        <div class="noticias-container">
            @forelse ($podcasts as $fonte => $episodios)
                <h4 class="noticias-fonte">{{ $fonte }}</h4>
                <div class="noticias-grid">
                    @foreach ($episodios as $item)
                        <div class="noticia-card">
                            <img src="{{ $item['imagem'] ?? asset('images/podcast.png') }}" alt="{{ $item['titulo'] }}" class="noticia-img">
                            @if(!empty($item['has_audio']) && $item['has_audio'])
                                <button class="play-central" onclick="window.dispatchEvent(new CustomEvent('playAudio', { detail: { audioUrl: '{{ $item['media_url'] }}', title: '{{ addslashes($item['titulo']) }}' } }))">
                                    <i class="bi bi-play-circle"></i> Ouvir no player
                                </button>
                            @endif
                            <a href="{{ $item['link'] }}" target="_blank" class="noticia-title">
                                {{ $item['titulo'] }}
                            </a>
                            <div class="noticia-date" title="{{ $item['publicado_em_iso'] }}">
                                {{ $item['publicado_em'] }}
                            </div>
                            <div class="noticia-desc">
                                {{ $item['resumo'] }}
                            </div>
                            <a href="{{ $item['link'] }}" target="_blank" class="noticia-link">
                                Ouvir na fonte
                            </a>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="noticias-grid">
                    <p class="text-center text-white/60">Nenhum podcast disponível no momento.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Scripts adicionais se necessário --}}
@endpush
