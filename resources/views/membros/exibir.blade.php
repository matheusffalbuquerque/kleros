@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
@php
    use Illuminate\Support\Carbon;

    $members = trans('members');
    $common = $members['common'];
    $view = $members['view'];
    $formatDate = fn ($value) => $value ? Carbon::parse($value)->format('d/m/Y') : '-';
    $notInformed = $common['statuses']['not_informed'];
@endphp

<div class="container">
    <h1>{{ $view['title'] }}</h1>
    <form action="{{ route('membros.destroy', $membro->id) }}" method="post" onsubmit="return handleSubmit(event, this, '{{ $common['messages']['confirm_delete'] }}')">
        @csrf
        @method('DELETE')
        <div class="data-view">
            <div class="section">
                <h3>{{ $view['sections']['basic'] }}</h3>
                <div class="section-grid w100">
                    <div class="field full-width horizontal">
                        <div class="field-content">
                            <label for="nome">{{ $common['fields']['name'] }}:</label>
                            <div class="card-title">{{ $membro->nome }}</div>
                        </div>
                        <div class="avatar-container">
                            <img class="avatar_perfil" id="avatarPerfil" src="{{ asset('storage/' . ($membro->foto ?? 'images/newuser.png')) }}" alt="Avatar">
                            @if($membro->foto)
                            <div class="avatar-dropdown" id="avatarDropdown" hidden>
                                <button type="button" class="btn" data-action="remove-photo" data-member-id="{{ $membro->id }}">
                                    <i class="bi bi-trash"></i> Remover foto
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="field">
                        <label for="rg">{{ $common['fields']['rg'] }}:</label>
                        <div class="card-title">{{ $membro->rg ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="cpf">{{ $common['fields']['cpf'] }}:</label>
                        <div class="card-title">{{ $membro->cpf ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="data_nascimento">{{ $common['fields']['birthdate'] }}:</label>
                        <div class="card-title">{{ $formatDate($membro->data_nascimento) }}</div>
                    </div>
                    <div class="field">
                        <label for="telefone">{{ $common['fields']['phone'] }}:</label>
                        <div class="card-title">{{ $membro->telefone ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="estado_civil">{{ $common['fields']['marital_status'] }}:</label>
                        <div class="card-title">{{ optional($membro->estadoCiv)->titulo ?? $notInformed }}</div>
                    </div>
                    <div class="field">
                        <label for="escolaridade">{{ $common['fields']['education'] }}:</label>
                        <div class="card-title">{{ optional($membro->escolaridade)->titulo ?? $notInformed }}</div>
                    </div>
                    <div class="field">
                        <label for="profissao">{{ $common['fields']['profession'] }}:</label>
                        <div class="card-title">{{ $membro->profissao ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>{{ $view['sections']['address'] }}</h3>
                <div class="section-grid">
                    <div class="field">
                        <label for="endereco">{{ $common['fields']['address'] }}:</label>
                        <div class="card-title">{{ $membro->endereco ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="numero">{{ $common['fields']['number'] }}:</label>
                        <div class="card-title">{{ $membro->numero ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="complemento">{{ $common['fields']['complement'] }}:</label>
                        <div class="card-title">{{ $membro->complemento ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="bairro">{{ $common['fields']['district'] }}:</label>
                        <div class="card-title">{{ $membro->bairro ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="cep">{{ $common['fields']['postal_code'] }}:</label>
                        <div class="card-title">{{ $membro->cep ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>{{ $view['sections']['specifics'] }}</h3>
                <div class="section-grid">
                    <div class="field">
                        <label for="data_batismo">{{ $common['fields']['baptism_date'] }}:</label>
                        <div class="card-title">{{ $formatDate($membro->data_batismo) }}</div>
                    </div>
                    <div class="field">
                        <label for="denominacao_origem">{{ $common['fields']['origin_denomination'] }}:</label>
                        <div class="card-title">{{ $membro->denominacao_origem ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="ministerio">{{ $common['fields']['ministry'] }}:</label>
                        <div class="card-title">{{ optional($membro->ministerio)->titulo ?? $notInformed }}</div>
                    </div>
                    <div class="field">
                        <label for="data_consagracao">{{ $common['fields']['ordination_date'] }}:</label>
                        <div class="card-title">{{ $formatDate($membro->data_consagracao) }}</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>{{ $view['sections']['family'] }}</h3>
                <div class="section-grid">
                    <div class="field">
                        <label for="nome_paterno">{{ $common['fields']['father_name'] }}:</label>
                        <div class="card-title">{{ $membro->nome_paterno ?? '-' }}</div>
                    </div>
                    <div class="field">
                        <label for="nome_materno">{{ $common['fields']['mother_name'] }}:</label>
                        <div class="card-title">{{ $membro->nome_materno ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="form-options nao-imprimir limit-80">
                <a onclick="abrirJanelaModal('{{ route('membros.form_editar', $membro->id) }}')">
                    <button class="btn" type="button"><i class="bi bi-pencil-square"></i> {{ $view['actions']['edit'] }}</button>
                </a>
                <button class="btn imprimir" type="button"><i class="bi bi-printer"></i> {{ $view['actions']['print'] }}</button>
                <button class="btn" type="submit"><i class="bi bi-trash"></i> {{ $view['actions']['remove'] }}</button>
                <a href="{{ route('membros.painel') }}">
                    <button type="button" class="btn"><i class="bi bi-arrow-return-left"></i> {{ $view['actions']['back'] }}</button>
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.imprimir').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                window.print();
            });
        });

        // Dropdown do avatar
        const avatarPerfil = document.getElementById('avatarPerfil');
        const avatarDropdown = document.getElementById('avatarDropdown');

        if (avatarPerfil && avatarDropdown) {
            avatarPerfil.style.cursor = 'pointer';

            avatarPerfil.addEventListener('click', function(event) {
                event.stopPropagation();
                avatarDropdown.hidden = !avatarDropdown.hidden;
            });

            // Fechar ao clicar fora
            document.addEventListener('click', function(event) {
                if (!avatarPerfil.contains(event.target) && !avatarDropdown.contains(event.target)) {
                    avatarDropdown.hidden = true;
                }
            });

            // Ação de remover foto
            const removePhotoBtn = avatarDropdown.querySelector('[data-action="remove-photo"]');
            if (removePhotoBtn) {
                removePhotoBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    const memberId = this.dataset.memberId;

                    confirmarAcao('Tem certeza que deseja remover a foto do membro?').then((confirmed) => {
                        if (confirmed) {
                            fetch(`/membros/${memberId}/remover-foto`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                },
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    avatarPerfil.src = '{{ asset('storage/images/newuser.png') }}';
                                    avatarDropdown.remove();
                                    flashMsg(data.message || 'Foto removida com sucesso!', 'success');
                                } else {
                                    flashMsg(data.message || 'Erro ao remover foto.', 'error');
                                }
                            })
                            .catch(error => {
                                flashMsg('Erro ao remover foto.', 'error');
                                console.error('Error:', error);
                            });
                        }
                    });
                });
            }
        }
    });
</script>
@endpush
