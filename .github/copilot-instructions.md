# GitHub Copilot - Instruções do Projeto Kleros

## Contexto do Projeto
Este é um sistema de gerenciamento eclesiástico (ERP Church) chamado **Kleros**, construído com Laravel + Livewire.

## Arquitetura e Padrões

### Stack Tecnológica
- **Backend**: Laravel 11.x (PHP 8.2+)
- **Frontend**: Livewire 3.x, Alpine.js, Tailwind CSS
- **Banco de Dados**: MySQL
- **Autenticação**: Laravel Sanctum
- **Módulos**: Sistema modular em `/modules`

### Estrutura de Pastas
- `app/Models/` - Modelos Eloquent
- `app/Http/Controllers/` - Controllers tradicionais
- `app/Livewire/` - Componentes Livewire
- `modules/` - Módulos do sistema (Batismo, Bíblia, Células, etc.)
- `resources/views/` - Views Blade principais
- `resources/views/layouts/` - Layouts

### Padrões de Código

#### Models
- **Sempre** adicionar Global Scope para filtrar por `congregacao_id`
- Usar `protected $fillable` para mass assignment
- Relacionamentos devem usar type hints
- Exemplo:
```php
protected static function booted(): void
{
    static::addGlobalScope('congregacao', function (Builder $builder) {
        if (Auth::check() && Auth::user()->congregacao_id) {
            $builder->where('congregacao_id', Auth::user()->congregacao_id);
        }
    });
}
```

#### Controllers
- Usar `app('congregacao')` para obter a congregação ativa
- Sempre filtrar por `congregacao_id` nas queries
- Retornar JSON para requests AJAX
- Views devem estar em snake_case

#### Views Blade
- Layout principal: `@extends('layouts.main')`
- Usar `@section('title')` e `@section('content')`
- Classes CSS: preferir Tailwind quando possível
- Ícones: usar Bootstrap Icons (`bi bi-*`)

#### Livewire Components
- Usar traits quando aplicável
- Emitir eventos para comunicação entre componentes
- Validação com `$rules` property
- Real-time validation quando necessário

### Convenções de Nomenclatura

#### Banco de Dados
- Tabelas: plural, snake_case (ex: `culto_categorias`)
- Colunas: snake_case (ex: `congregacao_id`)
- Foreign keys: `{tabela_singular}_id`
- Timestamps: `created_at`, `updated_at`

#### PHP
- Classes: PascalCase (ex: `CultoCategoria`)
- Métodos: camelCase (ex: `registrarPresenca`)
- Variáveis: camelCase (ex: `$visitantesDia`)
- Constants: UPPER_SNAKE_CASE

#### JavaScript
- Variáveis: camelCase
- Constantes: camelCase
- Event listeners: arrow functions quando possível

### Segurança e Isolamento

#### Multi-tenancy (Congregações)
- **SEMPRE** filtrar por `congregacao_id`
- Usar Global Scopes nos models
- Validar permissões por congregação
- Helper: `app('congregacao')` retorna a congregação ativa

#### Autenticação
- CSRF token em todos os forms: `@csrf`
- Validar usuário autenticado: `Auth::check()`
- Middleware de autenticação em rotas protegidas

### Funcionalidades Específicas

#### Sistema de Cultos
- Permite múltiplos cultos no mesmo dia
- Navegação entre cultos com chevron buttons
- Registro de visitantes por culto
- Integração com eventos

#### Sistema de Visitantes
- Busca rápida com Select2
- Contagem de visitas automática
- Situação do visitante (Categoria)
- Registro de presença por data

#### Módulos
- Batismo, Bíblia, Células, Cursos, Drive, Futcristao, Moedas, Projetos, Recados
- Verificar se módulo está ativo: `module_enabled('nome_modulo')`
- Cada módulo tem sua própria estrutura em `/modules/{Nome}`

## Comandos Úteis

### Development
```bash
php artisan serve              # Iniciar servidor
php artisan livewire:make      # Criar componente Livewire
php artisan make:model         # Criar model
php artisan migrate            # Rodar migrations
php artisan optimize:clear     # Limpar cache
```

### Testing
```bash
php artisan test               # Rodar testes
./vendor/bin/pint              # Formatar código
./vendor/bin/phpstan analyse   # Análise estática
```

## Boas Práticas

### Performance
- Usar eager loading para evitar N+1: `->with(['relation'])`
- Cache para queries pesadas
- Lazy loading apenas quando necessário

### Manutenibilidade
- Comentários em português
- Código auto-explicativo
- Métodos pequenos e focados
- DRY (Don't Repeat Yourself)

### UI/UX
- Feedback visual para ações do usuário
- Loading states em operações assíncronas
- Confirmação para ações destrutivas
- Mensagens de erro claras

## Preferências de Código

### Quando sugerir código:
1. **Sempre** incluir verificação de `congregacao_id`
2. **Sempre** usar try-catch para operações críticas
3. **Sempre** validar inputs
4. **Preferir** Eloquent ORM sobre Query Builder
5. **Preferir** componentes Livewire para interatividade
6. **Evitar** JavaScript vanilla quando Livewire/Alpine resolver
7. **Usar** traduções quando aplicável: `trans()`, `__()`, `@lang()`

### Formato de respostas:
- Explicações em português (pt-BR)
- Código com comentários quando necessário
- Exemplos práticos
- Avisos sobre segurança/performance quando relevante

## Contexto de Negócio
- Sistema para igrejas evangélicas (principalmente Assembleia de Deus)
- Multi-congregação (várias igrejas em uma instalação)
- Funcionalidades: cultos, visitantes, membros, células, batismos, eventos, etc.
- Usuários: pastores, secretários, líderes, membros

## Idioma
- Interface: Português (Brasil)
- Código: Inglês para nomes técnicos, português para domínio de negócio
- Comentários: Português
- Documentação: Português
