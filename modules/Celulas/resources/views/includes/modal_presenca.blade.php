<h1>Adicionar presente</h1>
<div class="info">
    <h3>Registro de presença do encontro</h3>
    <form class="form-control modal-presenca-form nao-imprimir" onsubmit="return false;">
        @csrf
        <input type="hidden" name="celula_id" value="{{ $celulaId }}">
        <input type="hidden" name="data_encontro" value="{{ $dataEncontro }}">
        <input type="hidden" name="return_to" value="{{ $panelUrl }}">

        <div class="form-item">
            <label>Tipo de participante</label>
            <div class="form-square modal-presenca-switch">
                <label>
                    <input type="radio" name="tipo_participante" value="membro" checked>
                    <span>Membro</span>
                </label>
                <label>
                    <input type="radio" name="tipo_participante" value="visitante">
                    <span>Visitante</span>
                </label>
            </div>
        </div>

        <div class="modal-presenca-section" data-section="membro">
            <div class="form-item">
                <label for="modal-presenca-membro">Selecione o membro</label>
                <select id="modal-presenca-membro" name="membro_id" class="painel-select2" data-placeholder="Buscar por nome">
                    <option value=""></option>
                    @foreach ($membros as $membro)
                        <option value="{{ $membro->id }}">{{ $membro->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="modal-presenca-section" data-section="visitante" hidden>
            <div class="form-item">
                <label for="modal-presenca-nome">Nome do visitante</label>
                <input type="text" id="modal-presenca-nome" name="visitante_nome" placeholder="Informe o nome completo">
            </div>

            <div class="form-item">
                <label for="modal-presenca-telefone">Telefone</label>
                <input type="tel" id="modal-presenca-telefone" name="visitante_telefone" placeholder="(00) 00000-0000">
            </div>

            <div class="form-item">
                <label for="modal-presenca-data">Data da visita</label>
                <input type="date" id="modal-presenca-data" name="visitante_data" value="{{ $dataEncontro }}">
            </div>

            <div class="form-item">
                <label for="modal-presenca-situacao">Situação</label>
                <select id="modal-presenca-situacao" name="visitante_situacao">
                    <option value="">Selecione a situação</option>
                    @foreach ($situacoesVisitante as $situacao)
                        <option value="{{ $situacao->id }}">{{ $situacao->titulo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-item">
                <label for="modal-presenca-observacoes">Observações</label>
                <textarea id="modal-presenca-observacoes" name="visitante_observacoes" rows="3" placeholder="Informações adicionais"></textarea>
            </div>
        </div>

        <div class="form-options">
            <button class="btn" type="submit">
                <i class="bi bi-person-plus"></i> Registrar presença
            </button>
            <button class="btn" type="button" onclick="fecharJanelaModal()">
                <i class="bi bi-x-circle"></i> Cancelar
            </button>
        </div>
    </form>

    <div class="modal-presenca-info card">
        <p><i class="bi bi-people"></i>
            @if ($celula)
                Registro de presença para a célula <strong>{{ $celula->identificacao }}</strong> em {{ \Carbon\Carbon::parse($dataEncontro)->format('d/m/Y') }}.
            @else
                Selecione uma célula antes de adicionar participantes ao encontro.
            @endif
        </p>
    </div>
</div>

<script>
    (function () {
        const root = document.querySelector('.modal-presenca-form');
        if (!root) {
            return;
        }

        const toggleSections = function (tipo) {
            document.querySelectorAll('.modal-presenca-section').forEach((section) => {
                const isTarget = section.dataset.section === tipo;
                section.toggleAttribute('hidden', !isTarget);
            });
        };

        root.querySelectorAll('input[name="tipo_participante"]').forEach((radio) => {
            radio.addEventListener('change', function () {
                toggleSections(this.value);
            });
        });

        toggleSections(root.querySelector('input[name="tipo_participante"]:checked')?.value || 'membro');
    })();
</script>

<style>
    .modal-presenca-form .form-item {
        margin-bottom: 1rem;
    }

    .modal-presenca-switch {
        display: flex;
        gap: 1.5rem;
    }

    .modal-presenca-section[hidden] {
        display: none !important;
    }

    .modal-presenca-info {
        margin-top: 1rem;
    }
</style>
