# GitHub Actions - Configuração

## 📋 O que foi configurado

### 1. **Workflow de CI/CD** (`.github/workflows/laravel.yml`)
Executa automaticamente quando há push ou pull request nas branches `main` ou `develop`.

**Jobs incluídos:**
- ✅ **Tests**: Executa testes PHPUnit com MySQL
- ✅ **Code Quality**: Análise estática com PHPStan
- ✅ **Security**: Verifica vulnerabilidades nas dependências

### 2. **Workflow de Deploy** (`.github/workflows/deploy.yml`)
Executa automaticamente no push para `main` ou manualmente.

---

## 🔧 Configuração Necessária

### 1. Secrets do GitHub
Configure os secrets no seu repositório: **Settings → Secrets and variables → Actions**

#### Para Deploy via SSH (opcional):
```
SSH_HOST      = seu-servidor.com
SSH_USER      = usuario-ssh
SSH_KEY       = chave-privada-ssh
```

#### Para outros serviços:
```
DB_PASSWORD   = senha-do-banco
APP_KEY       = chave-da-aplicacao
```

### 2. Adicionar dependências de desenvolvimento

Execute no seu projeto local:

```bash
# Laravel Pint (Code Style)
composer require laravel/pint --dev

# PHPStan (Análise Estática)
composer require phpstan/phpstan --dev

# Criar configuração do PHPStan
cat > phpstan.neon << 'EOF'
parameters:
    level: 5
    paths:
        - app
        - routes
    excludePaths:
        - vendor
EOF
```

### 3. Verificar .env.example

Certifique-se que o arquivo `.env.example` tem todas as variáveis necessárias:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kleros
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🚀 Como usar

### Executar automaticamente:
1. Faça commit das alterações
2. Push para `main` ou `develop`
3. Acesse **Actions** no GitHub para ver o progresso

### Executar manualmente:
1. Vá em **Actions** no repositório
2. Selecione o workflow desejado
3. Clique em **Run workflow**

---

## 📊 Monitoramento

### Ver status dos workflows:
- Acesse: `https://github.com/matheusalbuquerque27/adjerusalem/actions`
- Badge de status: Adicione no README.md

```markdown
![Laravel CI](https://github.com/matheusalbuquerque27/adjerusalem/workflows/Laravel%20CI%2FCD/badge.svg)
```

---

## 🛠️ Personalização

### Ajustar versão do PHP:
Edite `php-version: '8.2'` nos arquivos de workflow

### Adicionar mais testes:
Edite a seção de testes no `laravel.yml`

### Configurar deploy:
Descomente e configure a seção de deploy no `deploy.yml`

---

## 📝 Próximos passos

1. ✅ Commit dos arquivos de workflow
2. ⚙️ Configurar secrets no GitHub
3. 🧪 Instalar ferramentas de dev (`pint`, `phpstan`)
4. 🚀 Push e verificar se os workflows executam
5. 🔒 Configurar proteção de branches (opcional)

---

## 🐛 Troubleshooting

### Erro: "composer command not found"
- Verifique a imagem do PHP no workflow

### Erro: "Database connection failed"
- Verifique as variáveis de ambiente do MySQL no workflow

### Erro: "Permission denied"
- Adicione: `chmod -R 777 storage bootstrap/cache`

---

## 📚 Recursos

- [GitHub Actions Docs](https://docs.github.com/actions)
- [Laravel Testing](https://laravel.com/docs/testing)
- [Laravel Pint](https://laravel.com/docs/pint)
- [PHPStan](https://phpstan.org/)
