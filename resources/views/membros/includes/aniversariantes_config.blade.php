@php
    $configMensagem = $configMensagem ?? null;
    $envioAutomatico = (int) old('envio_automatico', optional($configMensagem)->envio_automatico ? 1 : 0);
@endphp

<h1><i class="bi bi-envelope-paper"></i> Configurar mensagem de aniversário</h1>

@if (!empty($success))
    <div class="alert alert-success">{{ $success }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="info">
    <form method="POST" action="/membros/aniversariantes/config">
        @csrf
        <div class="form-control">
            <div class="form-item">
                <label for="envio_automatico">Envio automático:</label>
                <div class="form-square">
                    <div>
                        <input type="radio" name="envio_automatico" id="envio_automatico_sim" value="1" @checked($envioAutomatico === 1)>
                        <label for="envio_automatico_sim">Ativar</label>
                    </div>
                    <div>
                        <input type="radio" name="envio_automatico" id="envio_automatico_nao" value="0" @checked($envioAutomatico === 0)>
                        <label for="envio_automatico_nao">Desativar</label>
                    </div>
                </div>
            </div>
            <div class="form-item">
                <label for="assunto">Assunto do e-mail:</label>
                <input type="text" name="assunto" id="assunto" placeholder="Ex.: Feliz aniversário!" value="{{ old('assunto', optional($configMensagem)->assunto) }}" required>
            </div>
            <div class="form-item">
                <label for="mensagem">Mensagem:</label>
                <textarea name="mensagem" id="mensagem" rows="6" placeholder="Escreva a mensagem que será enviada..." required>{{ old('mensagem', optional($configMensagem)->mensagem) }}</textarea>
            </div>
            <div class="form-options center">
                <button type="submit" class="btn"><i class="bi bi-save"></i> Salvar</button>
                <button type="button" class="btn btn-light" onclick="fecharJanelaModal()"><i class="bi bi-x-circle"></i> Fechar</button>
            </div>
        </div>
    </form>
</div>
