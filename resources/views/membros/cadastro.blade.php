@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
@php
    $members = trans('members');
    $common = $members['common'];
    $cadastro = $members['cadastro'];
@endphp

@if ($errors->any())
    <div class="msg">
        <div class="error">
            <ul>
                <li>{{ $errors->first() }}</li>
            </ul>
        </div>
    </div>
@endif

<div class="container">
    <h1>{{ $cadastro['title'] }}</h1>
    <form action="{{ route('membros.store') }}" method="post">
        @csrf
        <div class="form-control">
            <div class="form-block">
                <h3>{{ $cadastro['sections']['basic'] }}</h3>
                <div class="form-item">
                    <label for="nome">{{ $common['fields']['name'] }}:*</label>
                    <input type="text" name="nome" id="nome" placeholder="{{ $common['placeholders']['name'] }}" value="{{ old('nome') }}">
                </div>
                <div class="form-item">
                    <label for="rg">{{ $common['fields']['rg'] }}:</label>
                    <input type="text" name="rg" id="rg" placeholder="{{ $common['placeholders']['rg'] }}" value="{{ old('rg') }}">
                </div>
                <div class="form-item">
                    <label for="cpf">{{ $common['fields']['cpf'] }}:</label>
                    <input type="text" name="cpf" id="cpf" placeholder="{{ $common['placeholders']['cpf'] ?? $common['fields']['cpf'] }}" value="{{ old('cpf') }}">
                </div>
                <div class="form-item">
                    <label for="data_nascimento">{{ $common['fields']['birthdate'] }}:*</label>
                    <input type="date" name="data_nascimento" id="data_nascimento" value="{{ old('data_nascimento') }}">
                </div>
                <div class="form-item">
                    <label for="sexo">{{ $common['fields']['gender'] }}:</label>
                    <select name="sexo" id="sexo">
                        <option value="M" @selected(old('sexo') == 'M')>{{ $common['gender']['male'] }}</option>
                        <option value="F" @selected(old('sexo') == 'F')>{{ $common['gender']['female'] }}</option>
                    </select>
                </div>
                <div class="form-item">
                    <label for="telefone">{{ $common['fields']['phone'] }}:*</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="{{ $common['placeholders']['phone'] }}" value="{{ old('telefone') }}">
                </div>
                <div class="form-item">
                    <label for="email">{{ $common['fields']['email'] }}:</label>
                    <input type="email" id="email" name="email" placeholder="{{ $common['placeholders']['email'] }}" value="{{ old('email') }}">
                </div>
                <div class="form-item">
                    <label for="estado_civil">{{ $common['fields']['marital_status'] }}:</label>
                    <select name="estado_civil" id="estado_civil">
                        @foreach ($estado_civil as $item)
                            <option value="{{ $item->id }}" @selected(old('estado_civil') == $item->id)>{{ $item->titulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-item">
                    <label for="escolaridade">{{ $common['fields']['education'] }}:</label>
                    <select name="escolaridade" id="escolaridade">
                        @foreach ($escolaridade as $item)
                            <option value="{{ $item->id }}" @selected(old('escolaridade') == $item->id)>{{ $item->titulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-item">
                    <label for="profissao">{{ $common['fields']['profession'] }}:</label>
                    <input type="text" name="profissao" id="profissao" placeholder="{{ $common['placeholders']['profession'] ?? $common['fields']['profession'] }}" value="{{ old('profissao') }}">
                </div>
            </div>

            <div class="form-block">
                <h3>{{ $cadastro['sections']['address'] }}</h3>
                <div class="form-item">
                    <label for="endereco">{{ $common['fields']['address'] }}:</label>
                    <input type="text" name="endereco" id="endereco" placeholder="{{ $common['placeholders']['address'] }}" value="{{ old('endereco') }}">
                </div>
                <div class="form-item">
                    <label for="numero">{{ $common['fields']['number'] }}:</label>
                    <input type="text" name="numero" id="numero" placeholder="{{ $common['placeholders']['number'] }}" value="{{ old('numero') }}">
                </div>
                <div class="form-item">
                    <label for="complemento">{{ $common['fields']['complement'] }}:</label>
                    <input type="text" name="complemento" id="complemento" placeholder="{{ $common['placeholders']['complement'] }}" value="{{ old('complemento') }}">
                </div>
                <div class="form-item">
                    <label for="bairro">{{ $common['fields']['district'] }}:</label>
                    <input type="text" name="bairro" id="bairro" placeholder="{{ $common['placeholders']['district'] }}" value="{{ old('bairro') }}">
                </div>
                <div class="form-item">
                    <label for="cep">{{ $common['fields']['postal_code'] }}:</label>
                    <input type="text" name="cep" id="cep" placeholder="{{ $common['placeholders']['postal_code'] }}" value="{{ old('cep') }}">
                </div>
            </div>

            <div class="form-block">
                <h3>{{ $cadastro['sections']['specifics'] }}</h3>
                <div class="form-item">
                    <label for="data_batismo">{{ $common['fields']['baptism_date'] }}:</label>
                    <input type="date" name="data_batismo" id="data_batismo" value="{{ old('data_batismo') }}">
                </div>
                <div class="form-item">
                    <label for="denominacao_origem">{{ $common['fields']['origin_denomination'] }}:</label>
                    <input type="text" name="denominacao_origem" id="denominacao_origem" placeholder="{{ $common['placeholders']['origin_denomination'] ?? $common['fields']['origin_denomination'] }}" value="{{ old('denominacao_origem') }}">
                </div>
                <div class="form-item">
                    <label for="ministerio">{{ $common['fields']['ministry'] }}:</label>
                    <select name="ministerio" id="ministerio">
                        <option value="">{{ $common['placeholders']['not_applicable'] ?? 'Não aplicável' }}</option>
                        @foreach ($ministerios as $item)
                            <option value="{{ $item->id }}" @selected(old('ministerio') == $item->id)>{{ $item->titulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-item">
                    <label for="data_consagracao">{{ $common['fields']['ordination_date'] }}:</label>
                    <input type="date" name="data_consagracao" id="data_consagracao" value="{{ old('data_consagracao') }}">
                </div>
            </div>

            <div class="form-block">
                <h3>{{ $cadastro['sections']['family'] }}</h3>
                <div class="form-item">
                    <label for="nome_paterno">{{ $common['fields']['father_name'] }}:</label>
                    <input type="text" name="nome_paterno" id="nome_paterno" placeholder="{{ $common['placeholders']['father_name'] ?? $common['fields']['father_name'] }}" value="{{ old('nome_paterno') }}">
                </div>
                <div class="form-item">
                    <label for="nome_materno">{{ $common['fields']['mother_name'] }}:</label>
                    <input type="text" name="nome_materno" id="nome_materno" placeholder="{{ $common['placeholders']['mother_name'] ?? $common['fields']['mother_name'] }}" value="{{ old('nome_materno') }}">
                </div>
            </div>

            <div class="form-options">
                <button class="btn" type="submit"><i class="bi bi-plus-circle"></i> {{ $common['buttons']['add_member'] }}</button>
                <a href="{{ route('membros.painel') }}">
                    <button type="button" class="btn"><i class="bi bi-arrow-return-left"></i> {{ $common['buttons']['back'] }}</button>
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
