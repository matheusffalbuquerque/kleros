@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
@php
    $edit = trans('congregations_edit');
    $tabs = $edit['tabs'];
    $sections = $edit['sections'];
    $placeholders = $edit['placeholders'];
    $scripts = $edit['scripts'];
    $logoPath = optional($congregacao->config)->logo_caminho ? asset('storage/' . $congregacao->config->logo_caminho) : '';
    $bannerPath = optional($congregacao->config)->banner_caminho ? asset('storage/' . $congregacao->config->banner_caminho) : '';
    $hasLogo = !empty($logoPath);
    $hasBanner = !empty($bannerPath);
@endphp

<div class="container">
    <h1>{{ $edit['title'] }}</h1>
    <form action="{{ url("/configuracoes/{$congregacao->id}") }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="info">
            <div class="tabs">
                <ul class="tab-menu">
                    <li class="active" data-tab="geral"><i class="bi bi-person"></i> {{ $tabs['general'] }}</li>
                    <li data-tab="visual"><i class="bi bi-journal-text"></i> {{ $tabs['visual'] }}</li>
                    <li data-tab="administrativo"><i class="bi bi-shield-lock"></i> {{ $tabs['administrative'] }}</li>
                </ul>
                <div class="tab-content card">
                    {{-- Dados Gerais --}}
                    <div id="geral" class="tab-pane form-control active">
                        <h3>{{ $sections['institutional']['title'] }}</h3>
                        <div class="form-control">
                            <div class="form-item">
                                <label for="identificacao">{{ $sections['institutional']['fields']['identification'] }}</label>
                                <input type="text" name="identificacao" id="identificacao" value="{{ $congregacao->identificacao }}">
                            </div>
                            <div class="form-item">
                                <label for="nome_curto">{{ $sections['institutional']['fields']['short_name'] }}</label>
                                <input type="text" name="nome_curto" id="nome_curto" value="{{ old('nome_curto', $congregacao->nome_curto) }}"
                                    placeholder="{{ $placeholders['short_name'] ?? '' }}">
                            </div>
                            <div class="form-item">
                                <label for="cnpj">{{ $sections['institutional']['fields']['cnpj'] }}</label>
                                <input type="text" name="cnpj" id="cnpj" value="{{ $congregacao->cnpj }}" placeholder="{{ $placeholders['cnpj'] }}">
                            </div>
                            <div class="form-item">
                                <label for="email">{{ $sections['institutional']['fields']['email'] }}</label>
                                <input type="email" name="email" id="email" value="{{ $congregacao->email }}" autocomplete="email" placeholder="{{ $placeholders['email'] }}">
                            </div>
                            <div class="form-item">
                                <label for="telefone">{{ $sections['institutional']['fields']['phone'] }}</label>
                                <input type="tel" name="telefone" id="telefone" value="{{ $congregacao->telefone }}" placeholder="{{ $placeholders['phone'] }}">
                            </div>
                        </div>
                        <h3>{{ $sections['location']['title'] }}</h3>
                        <div class="form-control">
                            <div class="form-item">
                                <label for="endereco">{{ $sections['location']['fields']['address'] }}</label>
                                <input type="text" name="endereco" id="endereco" value="{{ $congregacao->endereco }}">
                            </div>
                            <div class="form-item">
                                <label for="numero">{{ $sections['location']['fields']['number'] }}</label>
                                <input type="text" name="numero" id="numero" value="{{ $congregacao->numero }}">
                            </div>
                            <div class="form-item">
                                <label for="complemento">{{ $sections['location']['fields']['complement'] }}</label>
                                <input type="text" name="complemento" id="complemento" value="{{ $congregacao->complemento }}">
                            </div>
                            <div class="form-item">
                                <label for="bairro">{{ $sections['location']['fields']['district'] }}</label>
                                <input type="text" name="bairro" id="bairro" value="{{ $congregacao->bairro }}">
                            </div>
                            <div class="form-item">
                                <label for="pais">{{ $sections['location']['fields']['country'] }}</label>
                                <select name="pais" id="pais">
                                    <option value="">{{ $sections['location']['placeholders']['country'] }}</option>
                                    @foreach($paises as $item)
                                        <option value="{{ $item->id }}" @selected($congregacao->pais_id == $item->id)>{{ $item->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-item">
                                <label for="estado">{{ $sections['location']['fields']['state'] }}</label>
                                <select name="estado" id="estado">
                                    <option value="">{{ $sections['location']['placeholders']['state'] }}</option>
                                </select>
                            </div>
                            <div class="form-item">
                                <label for="cidade">{{ $sections['location']['fields']['city'] }}</label>
                                <select name="cidade" id="cidade">
                                    <option value="">{{ $sections['location']['placeholders']['city'] }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Visual --}}
                    <div id="visual" class="tab-pane form-control">
                        <h3>{{ $sections['visual']['title'] }}</h3>
                        <div class="form-control">
                            <h4>{{ $sections['visual']['files']['title'] }}</h4>
                            <div class="form-item">
                                <label for="logo">{{ $sections['visual']['files']['logo'] }}</label>
                                <div class="preview-wrapper" data-preview-container="logo">
                                    <img
                                        class="image-small{{ $hasLogo ? '' : ' is-hidden' }}"
                                        id="logo-img"
                                        src="{{ $hasLogo ? $logoPath : '' }}"
                                        alt="{{ $sections['visual']['files']['logo'] }}"
                                        data-preview-image
                                        data-preview-original="{{ $hasLogo ? $logoPath : '' }}">
                                    <div class="preview-placeholder{{ $hasLogo ? ' is-hidden' : '' }}" data-preview-placeholder>
                                        <i class="bi bi-image"></i>
                                    </div>
                                    <div class="loading-indicator is-hidden" data-preview-loader>
                                        <img src="{{ asset('storage/images/loading.gif') }}" alt="{{ $scripts['loading'] }}">
                                    </div>
                                </div>
                                <div class="logo">
                                    <span id="file-logo" data-preview-filename="logo" data-placeholder="{{ $scripts['no_file'] }}">{{ $scripts['no_file'] }}</span>
                                    <div style="display: flex; gap: 1px; align-items: center;flex-wrap: wrap;">
                                        <label for="logo" class="btn-line"><i class="bi bi-upload"></i> {{ $sections['visual']['files']['upload'] }}</label>
                                        @if(module_enabled('drive'))
                                        <label onclick="abrirModalImagem('logo')" class="btn-line"><i class="bi bi-hdd"></i> Drive</label>
                                        @endif
                                    </div>
                                    <input type="file" name="logo" id="logo" url="" accept="image/*" data-preview-input="logo">
                                    <input type="hidden" name="logo_acervo" id="logo_acervo">
                                </div>
                            </div>
                            <div class="form-item">
                                <label for="banner">{{ $sections['visual']['files']['banner'] }}</label>
                                <div class="preview-wrapper" data-preview-container="banner">
                                    <img
                                        class="image-small{{ $hasBanner ? '' : ' is-hidden' }}"
                                        id="banner-img"
                                        src="{{ $hasBanner ? $bannerPath : '' }}"
                                        alt="{{ $sections['visual']['files']['banner'] }}"
                                        data-preview-image
                                        data-preview-original="{{ $hasBanner ? $bannerPath : '' }}">
                                    <div class="preview-placeholder{{ $hasBanner ? ' is-hidden' : '' }}" data-preview-placeholder>
                                        <i class="bi bi-images"></i>
                                    </div>
                                    <div class="loading-indicator is-hidden" data-preview-loader>
                                        <img src="{{ asset('storage/images/loading.gif') }}" alt="{{ $scripts['loading'] }}">
                                    </div>
                                </div>
                                <div class="banner">
                                    <span id="file-banner" data-preview-filename="banner" data-placeholder="{{ $scripts['no_file'] }}">{{ $scripts['no_file'] }}</span>
                                    <div style="display: flex; gap: 1px; align-items: center;flex-wrap: wrap;">
                                        <label for="banner" class="btn-line"><i class="bi bi-upload"></i> {{ $sections['visual']['files']['upload'] }}</label>
                                        @if(module_enabled('drive'))
                                        <label onclick="abrirModalImagem('banner')" class="btn-line"><i class="bi bi-hdd"></i> Drive</label>
                                        @endif
                                    </div>
                                    <input type="file" name="banner" id="banner" url="" accept="image/*" data-preview-input="banner">
                                    <input type="hidden" name="banner_acervo" id="banner_acervo">
                                </div>
                            </div>
                        </div>

                        <h3>{{ $sections['visual']['colors']['title'] }}</h3>
                        <p class="hint">{{ $sections['visual']['colors']['description'] }}</p>
                        <div class="form-control">
                            <div class="form-item">
                                <label for="cor_primaria">{{ $sections['visual']['colors']['primary'] }}</label>
                                <input type="color" name="conjunto_cores[primaria]" id="cor_primaria" value="{{ $congregacao->config->conjunto_cores['primaria'] }}">
                            </div>
                            <div class="form-item">
                                <label for="cor_secundaria">{{ $sections['visual']['colors']['secondary'] }}</label>
                                <input type="color" name="conjunto_cores[secundaria]" id="cor_secundaria" value="{{ $congregacao->config->conjunto_cores['secundaria'] }}">
                            </div>
                            <div class="form-item">
                                <label for="cor_terciaria">{{ $sections['visual']['colors']['accent'] }}</label>
                                <input type="color" name="conjunto_cores[terciaria]" id="cor_terciaria" value="{{ $congregacao->config->conjunto_cores['terciaria'] }}">
                            </div>
                            <div class="form-item">
                                <label for="fonte">{{ $sections['visual']['colors']['font'] }}</label>
                                <select name="font_family" id="fonte">
                                    @foreach ($fontes as $fonte)
                                        <option value="{{ $fonte }}" @selected($congregacao->config->font_family === $fonte)>{{ $fonte }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-item">
                                <h4 class="w100 right">
                                    <div class="tag">{{ $sections['visual']['colors']['preview_label'] }}</div>
                                    <span class="right" id="font-preview">{{ $sections['visual']['colors']['preview_text'] }}</span>
                                </h4>
                            </div>
                        </div>

                        <h3>{{ $sections['visual']['themes']['title'] }}</h3>
                        <div class="form-control">
                            <div class="form-item">
                                <div class="form-square" id="tema">
                                    <div>
                                        <input type="radio" id="classico" name="tema" value="1" @checked(optional($congregacao->config->tema)->id == 1)>
                                        <label for="classico">{{ $sections['visual']['themes']['classic'] }}</label>
                                    </div>
                                    <div>
                                        <input type="radio" id="moderno" name="tema" value="2" @checked(optional($congregacao->config->tema)->id == 2)>
                                        <label for="moderno">{{ $sections['visual']['themes']['modern'] }}</label>
                                    </div>
                                    <div>
                                        <input type="radio" id="vintage" name="tema" value="3" @checked(optional($congregacao->config->tema)->id == 3)>
                                        <label for="vintage">{{ $sections['visual']['themes']['vintage'] }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Administração --}}
                    <div id="administrativo" class="tab-pane form-control">
                        <h3>{{ $sections['administrative']['title'] }}</h3>
                        <div class="form-control">
                            <div class="form-item">
                                <label for="agrupamentos">{{ $sections['administrative']['grouping'] }}</label>
                                <select name="agrupamentos" id="agrupamentos">
                                    @foreach($sections['administrative']['grouping_options'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('agrupamentos', $congregacao->config->agrupamentos) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-item">
                                <label>{{ $sections['administrative']['cells']['label'] }}</label>
                                <div class="form-square">
                                    <div>
                                        <input type="radio" id="celula_ativo" name="celulas" value="1" @checked($congregacao->config->celulas == 1)>
                                        <label for="celula_ativo">{{ $sections['administrative']['cells']['active'] }}</label>
                                    </div>
                                    <div>
                                        <input type="radio" id="celula_inativo" name="celulas" value="0" @checked($congregacao->config->celulas == 0)>
                                        <label for="celula_inativo">{{ $sections['administrative']['cells']['inactive'] }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-item">
                                <label>{{ $sections['administrative']['language']['label'] }}</label>
                                <select name="language" id="language">
                                    @foreach($languageOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('language', $congregacao->language) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-options">
                        <button class="btn" type="submit"><i class="bi bi-arrow-clockwise"></i> {{ $edit['buttons']['update'] }}</button>
                        <button class="btn" type="button"><i class="bi bi-skip-backward"></i> {{ $edit['buttons']['restore'] }}</button>
                        <a href="{{ url('/') }}"><button type="button" class="btn"><i class="bi bi-arrow-return-left"></i> {{ $edit['buttons']['back'] }}</button></a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .tab-menu {
        display: flex;
        justify-content: left;
        list-style: none;
        padding: 0;
        margin: 0 0 15px 0;
        border-bottom: 2px solid var(--secondary-color);
    }
    .tab-menu li {
        padding: 6px 12px;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        margin-right: 4px;
        border-radius: 8px 8px 0 0;
        transition: all .3s;
        font-weight: 500;
    }
    .tab-menu li:hover {
        background: var(--secondary-color);
        color: #fff;
    }
    .tab-menu li.active {
        background: var(--secondary-color);
        color: #fff;
        font-weight: bold;
    }
    .card {
        border-radius: 0 8px 8px 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .tab-pane {
        display: none;
        animation: fadeIn .4s ease-in-out;
    }
    .tab-pane.active {
        display: block;
    }
    .form-item input:focus,
    .form-item textarea:focus {
        border: 1px solid var(--primary-color);
        outline: none;
    }
    .image-small {
        max-width: 150px;
        border-radius: 10px;
        margin: 10px 0;
        display: block;
    }
    .preview-wrapper {
        position: relative;
        display: inline-block;
    }
    .preview-placeholder {
        width: 150px;
        height: 100px;
        border-radius: 10px;
        margin: 10px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f7f7f7;
        border: 1px dashed #d0d0d0;
        color: #888;
    }
    .preview-placeholder i {
        font-size: 32px;
    }
    .loading-indicator {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.85);
        border-radius: 10px;
    }
    .loading-indicator img {
        max-width: 64px;
    }
    .is-hidden {
        display: none !important;
    }
    #file-logo, #file-banner {
        max-width: 220px;
    }
    .form-options {
        text-align: center;
        margin-top: 20px;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(10px);}
        to {opacity: 1; transform: translateY(0);}
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const translations = @json($scripts);

        const tabs = document.querySelectorAll('.tab-menu li');
        const panes = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                panes.forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                const target = this.getAttribute('data-tab');
                document.getElementById(target)?.classList.add('active');
            });
        });

        $('#fonte').on('change', function() {
            $('#font-preview').css('font-family', this.value);
        });

        const previewInputs = document.querySelectorAll('[data-preview-input]');

        previewInputs.forEach((input) => {
            const key = input.dataset.previewInput;
            const container = document.querySelector(`[data-preview-container="${key}"]`);
            if (!container) {
                return;
            }

            const loader = container.querySelector('[data-preview-loader]');
            const previewImage = container.querySelector('[data-preview-image]');
            const placeholder = container.querySelector('[data-preview-placeholder]');
            const filenameLabel = document.querySelector(`[data-preview-filename="${key}"]`);
            const placeholderText = filenameLabel ? (filenameLabel.dataset.placeholder || translations.no_file) : translations.no_file;

            if (filenameLabel && !filenameLabel.dataset.placeholder) {
                filenameLabel.dataset.placeholder = placeholderText;
            }

            if (previewImage && !previewImage.dataset.previewOriginal) {
                previewImage.dataset.previewOriginal = previewImage.getAttribute('src') || '';
            }

            const restoreOriginal = () => {
                if (previewImage) {
                    const original = previewImage.dataset.previewOriginal || '';
                    if (original) {
                        previewImage.src = original;
                        previewImage.classList.remove('is-hidden');
                        placeholder?.classList.add('is-hidden');
                    } else {
                        previewImage.src = '';
                        previewImage.classList.add('is-hidden');
                        placeholder?.classList.remove('is-hidden');
                    }
                } else {
                    placeholder?.classList.remove('is-hidden');
                }

                if (filenameLabel) {
                    filenameLabel.textContent = filenameLabel.dataset.placeholder || translations.no_file;
                }
            };

            input.addEventListener('change', () => {
                const file = input.files && input.files[0] ? input.files[0] : null;

                if (filenameLabel) {
                    filenameLabel.textContent = file ? file.name : (filenameLabel.dataset.placeholder || translations.no_file);
                }

                if (file) {
                    placeholder?.classList.add('is-hidden');
                    previewImage?.classList.add('is-hidden');
                    loader?.classList.remove('is-hidden');

                    const reader = new FileReader();
                    reader.addEventListener('load', (event) => {
                        loader?.classList.add('is-hidden');
                        if (previewImage) {
                            previewImage.src = event.target?.result || '';
                            previewImage.classList.remove('is-hidden');
                        }
                    });
                    reader.addEventListener('error', () => {
                        loader?.classList.add('is-hidden');
                        restoreOriginal();
                    });
                    reader.readAsDataURL(file);
                } else {
                    loader?.classList.add('is-hidden');
                    restoreOriginal();
                }
            });
        });

        const paisSelect = document.getElementById('pais');
        const estadoSelect = document.getElementById('estado');
        const cidadeSelect = document.getElementById('cidade');
        const selectedPais = "{{ $congregacao->pais_id ?? '' }}";
        const selectedEstado = "{{ $congregacao->estado_id ?? '' }}";
        const selectedCidade = "{{ $congregacao->cidade_id ?? '' }}";

        if (paisSelect && estadoSelect && cidadeSelect) {
            paisSelect.addEventListener('change', function () {
                carregarEstados(this.value);
            });
            estadoSelect.addEventListener('change', function () {
                carregarCidades(this.value);
            });
            if (selectedPais) {
                carregarEstados(selectedPais, selectedEstado, () => {
                    if (selectedEstado) {
                        carregarCidades(selectedEstado, selectedCidade);
                    }
                });
            }
        }

        function carregarEstados(paisId, estadoId = null, callback = null) {
            estadoSelect.innerHTML = `<option value="">${translations.loading}</option>`;
            cidadeSelect.innerHTML = `<option value="">${translations.select_city}</option>`;

            if (!paisId) {
                estadoSelect.innerHTML = `<option value="">${translations.select_state}</option>`;
                return;
            }

            fetch(`/estados/${paisId}`)
                .then(res => res.json())
                .then(estados => {
                    estadoSelect.innerHTML = `<option value="">${translations.select_state}</option>`;
                    estados.forEach(estado => {
                        const selected = estadoId && Number(estado.id) === Number(estadoId) ? 'selected' : '';
                        estadoSelect.innerHTML += `<option value="${estado.id}" ${selected}>${estado.nome}</option>`;
                    });
                    if (callback) callback();
                });
        }

        function carregarCidades(estadoId, cidadeId = null) {
            cidadeSelect.innerHTML = `<option value="">${translations.loading}</option>`;

            if (!estadoId) {
                cidadeSelect.innerHTML = `<option value="">${translations.select_city}</option>`;
                return;
            }

            fetch(`/cidades/${estadoId}`)
                .then(res => res.json())
                .then(cidades => {
                    cidadeSelect.innerHTML = `<option value="">${translations.select_city}</option>`;
                    cidades.forEach(cidade => {
                        const selected = cidadeId && Number(cidade.id) === Number(cidadeId) ? 'selected' : '';
                        cidadeSelect.innerHTML += `<option value="${cidade.id}" ${selected}>${cidade.nome}</option>`;
                    });
                });
        }
    });

    // Gerenciamento de seleção de imagem do Drive para logo ou banner
    let campoImagemAtual = null;

    function abrirModalImagem(campo) {
        campoImagemAtual = campo;
        abrirJanelaModal('{{ route('arquivos.imagens') }}');
    }

    // Recebe a imagem selecionada do gestor de imagens (modal)
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'imagemSelecionada' && campoImagemAtual) {
            const { arquivoId, arquivoUrl } = event.data;
            const campo = campoImagemAtual;
            
            // Atualiza a preview da imagem
            const previewImage = document.querySelector(`[data-preview-container="${campo}"] [data-preview-image]`);
            const placeholder = document.querySelector(`[data-preview-container="${campo}"] [data-preview-placeholder]`);
            const filenameLabel = document.querySelector(`[data-preview-filename="${campo}"]`);
            const hiddenInput = document.getElementById(`${campo}_acervo`);
            
            if (previewImage) {
                previewImage.src = arquivoUrl;
                previewImage.classList.remove('is-hidden');
            }
            
            if (placeholder) {
                placeholder.classList.add('is-hidden');
            }
            
            if (filenameLabel) {
                // Extrai o nome do arquivo da URL
                const nomeArquivo = arquivoUrl.split('/').pop();
                filenameLabel.textContent = nomeArquivo;
            }
            
            // Armazena o ID do arquivo no campo hidden
            if (hiddenInput) {
                hiddenInput.value = arquivoId;
            }
            
            console.log(`Imagem do Drive selecionada para ${campo}:`, { id: arquivoId, url: arquivoUrl });
            
            // Reseta o campo atual
            campoImagemAtual = null;
        }
    });
</script>
@endpush
@endsection

