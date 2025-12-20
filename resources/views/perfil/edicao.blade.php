@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')

@php
    $fotoPath = !empty($membro->foto) ? Storage::url($membro->foto) : asset('storage/images/newuser.png');
@endphp

<div class="container">
    <h1>Editar Perfil</h1>
    <div class="info">
        <form action="{{route('perfil.update', $membro->id)}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="tabs">
                <!-- Menu de abas -->
                <ul class="tab-menu">
                    <li class="active" data-tab="pessoais"><i class="bi bi-person"></i> Dados Pessoais</li>
                    <li data-tab="endereco"><i class="bi bi-geo-alt"></i> Endereço</li>
                    <li data-tab="bio"><i class="bi bi-journal-text"></i> Biografia</li>
                    <li data-tab="seguranca"><i class="bi bi-shield-lock"></i> Segurança</li>
                </ul>

                <!-- Conteúdo das abas -->
                <div class="tab-content card">

                    <!-- Aba 1 -->
                    <div id="pessoais" class="tab-pane form-control active">
                        <div class="form-item">
                            <label for="nome">Nome completo</label>
                            <input type="text" id="nome" name="nome" 
                                value="{{ old('nome', optional($membro)->nome) }}" required>
                        </div>
                        <div class="form-item">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                value="{{ old('email', optional(auth()->user())->email) }}" required>
                        </div>
                        <div class="form-item">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" 
                                value="{{ old('telefone', optional($membro)->telefone) }}" required>
                        </div>
                    </div>

                    <!-- Aba Endereço -->
                    <div id="endereco" class="tab-pane form-control">
                        <div class="form-item">
                            <label for="endereco">Endereço</label>
                            <input type="text" id="endereco" name="endereco"
                                value="{{ old('endereco', optional($membro)->endereco) }}">
                        </div>
                        <div class="form-item">
                            <label for="numero">Número</label>
                            <input type="text" id="numero" name="numero"
                                value="{{ old('numero', optional($membro)->numero) }}">
                        </div>
                        <div class="form-item">
                            <label for="bairro">Bairro</label>
                            <input type="text" id="bairro" name="bairro"
                                value="{{ old('bairro', optional($membro)->bairro) }}">
                        </div>
                        <div class="form-item">
                            <label for="complemento">Complemento</label>
                            <input type="text" id="complemento" name="complemento"
                                value="{{ old('complemento', optional($membro)->complemento) }}">
                        </div>
                        <div class="form-item">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep"
                                value="{{ old('cep', optional($membro)->cep) }}">
                        </div>
                    </div>

                    <!-- Aba 2 -->
                    <div id="seguranca" class="tab-pane form-control">
                        <h4>Modificar senha</h4>
                        <div class="form-item">
                            <label for="senha_atual">Senha atual</label>
                            <input type="password" id="senha_atual" name="senha_atual" 
                                placeholder="Senha atual">
                        </div>
                        <div class="form-item">
                            <label for="nova_senha">Nova senha</label>
                            <input type="password" id="nova_senha" name="nova_senha" 
                                placeholder="Nova senha">
                        </div>
                    </div>

                    <!-- Aba 3 -->
                    <div id="bio" class="tab-pane form-control">
                        <div class="form-item">
                            <label for="foto">Foto de perfil</label>

                            <div class="preview-wrapper" data-preview-container="foto">
                                <img
                                    class="image-small image-preview"
                                    id="foto-img"
                                    src="{{ $fotoPath }}"
                                    alt="Foto de perfil"
                                    data-preview-image
                                    data-preview-original="{{ $fotoPath }}">
                                <div class="preview-placeholder is-hidden" data-preview-placeholder>
                                    <i class="bi bi-image"></i>
                                </div>
                                <div class="loading-indicator is-hidden" data-preview-loader>
                                    <img src="{{ asset('storage/images/loading.gif') }}" alt="Carregando">
                                </div>
                            </div>

                            <div class="foto-upload">
                                <span id="file-foto" data-preview-filename="foto" data-placeholder="Nenhum arquivo selecionado">Nenhum arquivo selecionado</span>
                                <label for="foto" class="btn-line"><i class="bi bi-upload"></i> Upload</label>
                                <input type="file" name="foto" id="foto" accept="image/*" data-preview-input="foto">
                            </div>
                        </div>
                        <div class="form-item">
                            <label for="bio">Biografia</label>
                            <textarea id="biografia" name="biografia" rows="6" placeholder="Escreva sua biografia para que outros te conheçam melhor">{{ old('bio', optional($membro)->biografia) }}</textarea>
                        </div>
                    </div>
                    <div class="form-options">
                    <button type="submit" class="btn"><i class="bi bi-arrow-clockwise"></i> Atualizar</button>
                </div>
                </div>
            </div>
            </div>
        </form>
        @include('noticias.includes.destaques')
    </div>
</div>

<!-- Estilo básico -->
<style>
/* Abas */
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

.perfil-tabs {
    width: 100%;
    max-width: 860px;
    margin: 0 auto;
}
.perfil-tabs .tabs {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.perfil-tabs .tab-content.card {
    width: 100%;
    box-sizing: border-box;
}

/* Conteúdo das abas */
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

/* Foto */
.image-small,
.image-preview {
    max-width: 150px;
    border-radius: 10px;
    margin: 10px 0;
    display: block;
}
.foto-upload {
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn-upload {
    background: var(--secondary-color);
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    transition: background .3s;
}
.btn-upload:hover {
    background: var(--primary-color);
}
#foto {
    display: none;
}
.preview-wrapper {
    position: relative;
    display: inline-block;
}
.preview-placeholder {
    width: 150px;
    height: 150px;
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

#file-foto {
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Responsividade para foto-upload */
@media (max-width: 580px) {
    .foto-upload {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    #file-foto {
        max-width: 100%;
        width: 100%;
    }

    .btn-line {
        width: 100%;
        text-align: center;
        display: block;
    }
}

/* Botão salvar */
.form-options {
    text-align: center;
    margin-top: 20px;
}

/* Animação suave */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(10px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector('[data-preview-input="foto"]');
        if (!input) {
            return;
        }

        const container = document.querySelector('[data-preview-container="foto"]');
        const loader = container?.querySelector('[data-preview-loader]');
        const previewImage = container?.querySelector('[data-preview-image]');
        const placeholder = container?.querySelector('[data-preview-placeholder]');
        const filenameLabel = document.querySelector('[data-preview-filename="foto"]');
        const placeholderText = filenameLabel ? (filenameLabel.dataset.placeholder || filenameLabel.textContent || 'Nenhum arquivo selecionado') : 'Nenhum arquivo selecionado';

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
                filenameLabel.textContent = filenameLabel.dataset.placeholder || placeholderText;
            }
        };

        input.addEventListener('change', () => {
            const file = input.files && input.files[0] ? input.files[0] : null;

            if (filenameLabel) {
                filenameLabel.textContent = file ? file.name : (filenameLabel.dataset.placeholder || placeholderText);
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
</script>
@endpush

@endsection
