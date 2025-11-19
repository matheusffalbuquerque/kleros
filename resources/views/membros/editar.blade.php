@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')

@if ($errors->any())
    <div class="msg">
        <div class="error">
            <ul>
                {{$errors->first()}}
            </ul>
        </div>
    </div>
@endif

<div class="container">
    @include('membros.includes.form_editar')
</div>

@endsection