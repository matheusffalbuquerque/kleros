# Sistema de Navegação entre Modais

## 📋 Visão Geral

O sistema de modais do Kleros agora suporta **navegação em pilha**, permitindo que você abra um modal dentro de outro e volte ao anterior sem perder os dados preenchidos.

## 🎯 Casos de Uso

### Exemplo Prático: Editando Culto e Criando Evento

**Problema anterior:**
- Você está editando um culto
- Precisa criar um novo evento para vincular ao culto
- Ao clicar em "Cadastrar evento", o modal de edição do culto fechava
- Perdia todos os dados preenchidos

**Solução atual:**
- O sistema salva automaticamente o estado do modal atual
- Abre o novo modal (criar evento) por cima
- Após criar o evento, você pode voltar ao modal anterior
- Todos os dados são preservados

## 🚀 Como Usar

### 1. Uso Básico (Substituição Automática)

```javascript
// Comportamento padrão: substitui o modal atual
abrirJanelaModal('{{ route("eventos.form_criar") }}');
```

### 2. Modal Aninhado (Empilhamento)

```javascript
// Mantém o modal atual e abre um novo por cima
abrirJanelaModal('{{ route("eventos.form_criar") }}', { nested: true });
```

### 3. Voltar ao Modal Anterior

O botão de voltar aparece automaticamente quando há histórico:

```javascript
// Via JavaScript
voltarModalAnterior();

// Via botão na interface (aparece automaticamente)
// Clique no ícone de seta para a esquerda no topo do modal
```

### 4. Fechar Todos os Modais

```javascript
// Fecha todos os modais e limpa a pilha
fecharJanelaModal();
```

## 🔧 Funcionalidades Avançadas

### Atualizar Dados ao Voltar

Quando você criar um evento e voltar ao modal de edição de culto:

```javascript
// No formulário de criação de evento, após salvar:
window.addEventListener('eventoCreated', function(e) {
    const eventoId = e.detail.eventoId;
    const eventoTitulo = e.detail.eventoTitulo;
    
    // Volta ao modal anterior
    voltarModalAnterior();
    
    // Atualiza o select de eventos no modal anterior
    setTimeout(() => {
        const selectEvento = document.getElementById('evento_id');
        if (selectEvento) {
            const option = new Option(eventoTitulo, eventoId, true, true);
            selectEvento.add(option);
            selectEvento.value = eventoId;
        }
    }, 100);
});
```

### Verificar se há Modal Anterior

```javascript
if (modalStack.length > 0) {
    console.log('Há ' + modalStack.length + ' modal(is) no histórico');
}
```

### Limpar Pilha Manualmente

```javascript
// Limpa o histórico mas mantém o modal atual aberto
modalStack.length = 0;
currentModalIndex = -1;
```

## 📝 Exemplos Práticos

### Exemplo 1: Editar Culto → Criar Evento

```html
<!-- No formulário de edição de culto -->
<div class="form-item">
    <label for="evento_id">Evento: </label>
    <select name="evento_id" id="evento_id">
        <option value="">Selecione um evento</option>
        @foreach ($eventos as $item)
            <option value="{{ $item->id }}">{{ $item->titulo }}</option>
        @endforeach
    </select>
    <p>
        Não encontrou o evento? 
        <a onclick="abrirJanelaModal('{{ route('eventos.form_criar') }}')" class="link-standard">
            Cadastrar aqui
        </a>
    </p>
</div>
```

### Exemplo 2: Criar Membro → Adicionar Foto

```html
<!-- No formulário de criação de membro -->
<div class="form-item">
    <label>Foto de Perfil:</label>
    <img id="preview-foto" src="/images/default-avatar.png" />
    <button type="button" onclick="abrirJanelaModal('{{ route('arquivos.imagens') }}')">
        Selecionar Foto
    </button>
</div>
```

### Exemplo 3: Configurar Evento → Gerenciar Departamentos

```html
<!-- No formulário de evento -->
<div class="form-item">
    <label for="departamento_id">Departamento Responsável:</label>
    <select name="departamento_id" id="departamento_id">
        @foreach ($departamentos as $dept)
            <option value="{{ $dept->id }}">{{ $dept->nome }}</option>
        @endforeach
    </select>
    <button type="button" onclick="abrirJanelaModal('{{ route('departamentos.form_criar') }}')">
        Criar Departamento
    </button>
</div>
```

## 🎨 Interface Visual

### Botões de Navegação

- **Botão X (Fechar)**: Fecha todos os modais e limpa a pilha
- **Botão ← (Voltar)**: Aparece automaticamente quando há histórico, volta ao modal anterior

### Posicionamento dos Botões

```
┌─────────────────────────────────┐
│  ←  (Voltar)         X  (Fechar)│
│                                  │
│     Conteúdo do Modal            │
│                                  │
└─────────────────────────────────┘
```

## 🔒 Segurança e Validação

O sistema automaticamente:

1. ✅ Preserva o HTML completo do modal anterior
2. ✅ Reinicializa os scripts após voltar
3. ✅ Mantém os event listeners funcionando
4. ✅ Limpa a pilha ao fechar completamente
5. ✅ Suporta múltiplos níveis de aninhamento

## 🐛 Troubleshooting

### Problema: Dados não são preservados ao voltar

**Solução:** Certifique-se de que os inputs têm atributos `name` e `id` únicos.

### Problema: Scripts não funcionam no modal restaurado

**Solução:** O sistema reinicializa automaticamente, mas se usar jQuery, garanta que os eventos sejam delegados:

```javascript
// ❌ Não funciona após restaurar
$('#meuBotao').click(function() { ... });

// ✅ Funciona após restaurar
$(document).on('click', '#meuBotao', function() { ... });
```

### Problema: Botão de voltar não aparece

**Solução:** Verifique se o modal anterior foi salvo na pilha. Use `console.log(modalStack)` para debug.

## 📊 Arquitetura Técnica

### Estrutura de Dados

```javascript
const modalStack = [
    {
        url: '/eventos/criar',
        content: '<html>...</html>',
        iframe: false,
        title: 'Criar Evento'
    },
    // ... mais modais
];
```

### Fluxo de Execução

1. **Abrir Novo Modal**
   - Salva HTML do modal atual na pilha
   - Carrega novo conteúdo
   - Adiciona botão de voltar

2. **Voltar Modal**
   - Remove último item da pilha
   - Restaura HTML anterior
   - Reinicializa scripts
   - Atualiza botão de voltar

3. **Fechar Todos**
   - Limpa pilha completamente
   - Remove botão de voltar
   - Fecha modal overlay

## 🎯 Boas Práticas

1. **Use IDs únicos** para todos os campos de formulário
2. **Delegue eventos** para garantir funcionamento após restauração
3. **Evite aninhamento excessivo** (máximo 3-4 níveis)
4. **Comunique entre modais** usando eventos personalizados
5. **Teste navegação** após qualquer alteração nos modais

## 📚 Referências

- Código principal: `/resources/views/layouts/main.blade.php`
- Estilos: `/resources/css/base/style.scss`
- Exemplos: `/resources/views/cultos/includes/form_editar.blade.php`

---

**Versão:** 1.0.0  
**Data:** 06/12/2025  
**Autor:** Sistema Kleros
