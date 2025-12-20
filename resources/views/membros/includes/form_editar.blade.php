@php
    $members = trans('members');
    $common = $members['common'];
    $edit = $members['edit'];
    $permissoesSelecionadas = collect($permissoesSelecionadas ?? optional($membro->user)->getRoleNames()->toArray())
        ->filter()
        ->values()
        ->all();
@endphp

<h1>{{ $edit['title'] }}</h1>
<div class="info">
    <form action="{{ route('membros.atualizar', $membro->id) }}" method="post">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="tabs">
            <ul class="tab-menu">
                <li class="active" data-tab="membro-dados"><i class="bi bi-person-badge"></i> {{ $edit['tabs']['personal'] }}</li>
                <li data-tab="membro-endereco"><i class="bi bi-geo-alt"></i> {{ $edit['tabs']['address'] }}</li>
                <li data-tab="membro-outros"><i class="bi bi-people"></i> {{ $edit['tabs']['other'] }}</li>
                <li data-tab="membro-configuracoes"><i class="bi bi-gear"></i> {{ $edit['tabs']['settings'] ?? 'Configurações' }}</li>
            </ul>

            <div class="tab-content card">
                <div id="membro-dados" class="tab-pane form-control active">
                    <div class="form-item">
                        <label for="nome">{{ $common['fields']['name'] }}:*</label>
                        <input type="text" name="nome" id="nome" placeholder="{{ $common['placeholders']['name'] }}" value="{{ old('nome', $membro->nome) }}" required>
                        @error('nome')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-item">
                        <label for="rg">{{ $common['fields']['rg'] }}:</label>
                        <input type="text" name="rg" id="rg" placeholder="{{ $common['placeholders']['rg'] }}" value="{{ old('rg', $membro->rg) }}">
                    </div>
                    <div class="form-item">
                        <label for="cpf">{{ $common['fields']['cpf'] }}:</label>
                        <input type="text" name="cpf" id="cpf" placeholder="{{ $common['placeholders']['cpf'] ?? $common['fields']['cpf'] }}" value="{{ old('cpf', $membro->cpf) }}">
                    </div>
                    <div class="form-item">
                        <label for="data_nascimento">{{ $common['fields']['birthdate'] }}:*</label>
                        <input type="date" name="data_nascimento" id="data_nascimento" value="{{ old('data_nascimento', $membro->data_nascimento  ? \Carbon\Carbon::parse($membro->data_nascimento)->format('Y-m-d') : '') }}" required>
                    </div>
                    <div class="form-item">
                        <label for="sexo">{{ $common['fields']['gender'] }}:</label>
                        <select name="sexo" id="sexo">
                            <option value="Masculino" @selected(old('sexo', $membro->sexo) == 'Masculino')>{{ $common['gender']['male'] }}</option>
                            <option value="Feminino" @selected(old('sexo', $membro->sexo) == 'Feminino')>{{ $common['gender']['female'] }}</option>
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="telefone">{{ $common['fields']['phone'] }}:*</label>
                        <input type="text" name="telefone" id="telefone" placeholder="{{ $common['placeholders']['phone'] }}" value="{{ old('telefone', $membro->telefone) }}" required>
                    </div>
                    <div class="form-item">
                        <label for="email">{{ $common['fields']['email'] }}:</label>
                        <input type="email" name="email" id="email" placeholder="{{ $common['placeholders']['email'] }}" value="{{ old('email', $membro->email) }}">
                    </div>
                    <div class="form-item">
                        <label for="estado_civil">{{ $common['fields']['marital_status'] }}:</label>
                        <select name="estado_civil" id="estado_civil">
                            @foreach ($estado_civil as $item)
                                <option value="{{ $item->id }}" @selected(old('estado_civil', $membro->estado_civ_id) == $item->id)>
                                    {{ $item->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="escolaridade">{{ $common['fields']['education'] }}:</label>
                        <select name="escolaridade" id="escolaridade">
                            @foreach ($escolaridade as $item)
                                <option value="{{ $item->id }}" @selected(old('escolaridade', $membro->escolaridade_id) == $item->id)>
                                    {{ $item->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="profissao">{{ $common['fields']['profession'] }}:</label>
                        <input type="text" name="profissao" id="profissao" placeholder="{{ $common['placeholders']['profession'] ?? $common['fields']['profession'] }}" value="{{ old('profissao', $membro->profissao) }}">
                    </div>
                </div>

                <div id="membro-endereco" class="tab-pane form-control">
                    <div class="form-item">
                        <label for="endereco">{{ $common['fields']['address'] }}:</label>
                        <input type="text" name="endereco" id="endereco" placeholder="{{ $common['placeholders']['address'] }}" value="{{ old('endereco', $membro->endereco) }}">
                    </div>
                    <div class="form-item">
                        <label for="numero">{{ $common['fields']['number'] }}:</label>
                        <input type="text" name="numero" id="numero" placeholder="{{ $common['placeholders']['number'] }}" value="{{ old('numero', $membro->numero) }}">
                    </div>
                    <div class="form-item">
                        <label for="complemento">{{ $common['fields']['complement'] ?? 'Complemento' }}:</label>
                        <input type="text" name="complemento" id="complemento" placeholder="{{ $common['placeholders']['complement'] ?? 'Complemento' }}" value="{{ old('complemento', $membro->complemento) }}">
                    </div>
                    <div class="form-item">
                        <label for="bairro">{{ $common['fields']['district'] }}:</label>
                        <input type="text" name="bairro" id="bairro" placeholder="{{ $common['placeholders']['district'] }}" value="{{ old('bairro', $membro->bairro) }}">
                    </div>
                </div>

                <div id="membro-outros" class="tab-pane form-control">
                    <div class="form-item">
                        <label for="batizado">{{ $common['fields']['baptized'] ?? 'Batizado' }}:</label>
                        <select name="batizado" id="batizado">
                            <option value="1" @selected(old('batizado', $membro->batizado) == true)>Sim</option>
                            <option value="0" @selected(old('batizado', $membro->batizado) == false)>Não</option>
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="data_batismo">{{ $common['fields']['baptism_date'] }}:</label>
                        <input type="date" name="data_batismo" id="data_batismo" value="{{ old('data_batismo', $membro->data_batismo) }}">
                    </div>
                    <div class="form-item">
                        <label for="denominacao_origem">{{ $common['fields']['origin_denomination'] }}:</label>
                        <input type="text" name="denominacao_origem" id="denominacao_origem" placeholder="{{ $common['placeholders']['origin_denomination'] ?? $common['fields']['origin_denomination'] }}" value="{{ old('denominacao_origem', $membro->denominacao_origem) }}">
                    </div>
                    <div class="form-item">
                        <label for="ministerio">{{ $common['fields']['ministry'] }}:</label>
                        <select name="ministerio" id="ministerio">
                            <option value="">{{ $common['placeholders']['not_applicable'] ?? 'Não aplicável' }}</option>
                            @foreach ($ministerios as $item)
                                <option value="{{ $item->id }}" @selected(old('ministerio', $membro->ministerio_id) == $item->id)>
                                    {{ $item->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="data_consagracao">{{ $common['fields']['ordination_date'] }}:</label>
                        <input type="date" name="data_consagracao" id="data_consagracao" value="{{ old('data_consagracao', $membro->data_consagracao ? \Carbon\Carbon::parse($membro->data_consagracao)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="form-item">
                        <label for="nome_paterno">{{ $common['fields']['father_name'] }}:</label>
                        <input type="text" name="nome_paterno" id="nome_paterno" placeholder="{{ $common['placeholders']['father_name'] ?? $common['fields']['father_name'] }}" value="{{ old('nome_paterno', $membro->nome_paterno) }}">
                    </div>
                    <div class="form-item">
                        <label for="nome_materno">{{ $common['fields']['mother_name'] }}:</label>
                        <input type="text" name="nome_materno" id="nome_materno" placeholder="{{ $common['placeholders']['mother_name'] ?? $common['fields']['mother_name'] }}" value="{{ old('nome_materno', $membro->nome_materno) }}">
                    </div>
                </div>

                <div id="membro-configuracoes" class="tab-pane form-control">
                    <div class="form-item">
                        <label>{{ $common['fields']['permissions'] ?? 'Permissões' }}:</label>
                        @if(!empty($permissoesSelecionadas))
                            <div class="chip-row">
                                @foreach ($permissoesSelecionadas as $permissao)
                                    <span class="chip">{{ \Illuminate\Support\Str::headline($permissao) }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="muted">{{ $common['statuses']['not_informed'] ?? 'Nenhuma permissão definida' }}</div>
                        @endif
                    </div>
                    <div class="form-item">
                        <label for="ativo">{{ $common['fields']['status'] ?? 'Situação do Membro' }}:</label>
                        <select name="ativo" id="ativo">
                            <option value="1" @selected(old('ativo', $membro->ativo) == 1)>{{ $common['status']['active'] ?? 'Ativo' }}</option>
                            <option value="0" @selected(old('ativo', $membro->ativo) == 0)>{{ $common['status']['inactive'] ?? 'Desligado' }}</option>
                        </select>
                    </div>
                    <div class="form-item" id="motivo_desligamento_div" style="display: {{ old('ativo', $membro->ativo) == 0 ? 'flex' : 'none' }};">
                        <label for="motivo_desligamento">{{ $common['fields']['disconnection_reason'] ?? 'Motivo do Desligamento' }}:</label>
                        <textarea name="motivo_desligamento" id="motivo_desligamento" cols="30" rows="5">{{ old('motivo_desligamento', $ultimoMotivoDesligamento ?? '') }}</textarea>
                    </div>
                </div>

            <div class="form-options center">
                <button class="btn" type="submit"><i class="bi bi-arrow-clockwise"></i> {{ $common['buttons']['update_member'] }}</button>
                <button type="button" onclick="fecharJanelaModal()" class="btn"><i class="bi bi-x-circle"></i> {{ $common['buttons']['cancel'] }}</button>
            </div>
        </div>
    </form>
</div>
