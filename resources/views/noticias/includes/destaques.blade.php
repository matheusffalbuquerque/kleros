<div class="destaques-banner">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            @foreach ($destaques as $item)
                <div class="swiper-slide">
                    <div class="slide-content">
                        @if (!empty($item['imagem']))
                            <img src="{{ $item['imagem'] }}" alt="{{ $item['titulo'] }}" class="slide-image" onerror="this.style.display='none'">
                        @else
                            <div class="slide-image-placeholder">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                        <div class="slide-text">
                            <a href="{{ $item['link'] }}" target="_blank" class="slide-title" title="{{ $item['titulo'] }}">
                                {{ strlen(strip_tags($item['titulo'])) > 90 ? substr(strip_tags($item['titulo']), 0, 77) . '...' : strip_tags($item['titulo']) }}
                            </a>
                            <span class="slide-date" title="{{ $item['publicado_em_iso'] }}">{{ $item['publicado_em'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
