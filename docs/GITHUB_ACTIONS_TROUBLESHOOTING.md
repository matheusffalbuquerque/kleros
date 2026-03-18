# 🔧 Troubleshooting GitHub Actions - Kleros

## 🚨 Problemas Comuns e Soluções

### 1. SSH Timeout (dial tcp: i/o timeout)

**Erro:**
```
dial tcp ***:***: i/o timeout
Error: Process completed with exit code 1
```

**Causas Possíveis:**
- Firewall bloqueando IPs do GitHub Actions
- Servidor temporariamente inacessível
- Problemas de rede do GitHub Actions
- Porta SSH incorreta nos secrets

**Soluções:**

#### A. Verificar IP do Servidor
```bash
# No servidor
curl -4 ifconfig.me
```

#### B. Verificar se SSH está rodando
```bash
sudo systemctl status sshd
ss -tlnp | grep :22
```

#### C. Permitir IPs do GitHub Actions no Firewall
```bash
# Adicionar ranges de IP do GitHub Actions
# Veja: https://api.github.com/meta

# Exemplo com UFW
sudo ufw allow from 192.30.252.0/22 to any port 22
sudo ufw allow from 185.199.108.0/22 to any port 22
sudo ufw allow from 140.82.112.0/20 to any port 22
```

#### D. Verificar Secrets no GitHub
1. Acesse: `Settings > Secrets and variables > Actions`
2. Verifique se existem:
   - `SSH_HOST` - IP ou domínio do servidor
   - `SSH_USER` - Usuário SSH (ex: root)
   - `SSH_KEY` - Chave privada SSH
   - `SSH_PORT` - Porta SSH (geralmente 22)

#### E. Testar Conexão SSH Manualmente
```bash
# No seu computador local
ssh -i sua_chave_privada usuario@servidor -p 22 -v
```

#### F. Aumentar Timeout (JÁ APLICADO)
Os workflows já têm:
- `timeout: 120s` - Timeout de conexão
- `command_timeout: 30m` - Timeout de comandos

---

### 2. Erro de APP_KEY (file_get_contents failed)

**Erro:**
```
file_get_contents(.env): Failed to open stream
```

**Solução:** ✅ JÁ CORRIGIDO
- Ordem de execução dos steps foi corrigida
- `composer install` agora roda antes de `php artisan key:generate`

---

### 3. Build de Assets Falhando

**Erro:**
```
npm run build failed
```

**Soluções:**
```bash
# Verificar versão do Node no workflow
# Se necessário, especificar versão:
- name: Setup Node
  uses: actions/setup-node@v3
  with:
    node-version: '18'
```

---

### 4. Permissions Denied

**Erro:**
```
Permission denied
```

**Soluções:**
```bash
# No servidor
sudo chown -R www-data:www-data /var/www/kleros
sudo chmod -R 755 /var/www/kleros
sudo chmod -R 775 /var/www/kleros/storage
sudo chmod -R 775 /var/www/kleros/bootstrap/cache
```

---

### 5. Deploy Manual via Workflow Dispatch

Se o deploy automático não estiver funcionando, você pode:

1. **Ir para GitHub:**
   - `Actions > Deploy to Production > Run workflow`
   - Selecionar a branch `main`
   - Clicar em "Run workflow"

2. **Ou fazer deploy manual no servidor:**
```bash
cd /var/www/kleros

# Ativar modo manutenção
php artisan down

# Atualizar código
git pull origin main

# Instalar dependências
composer install --no-dev --optimize-autoloader
npm ci --omit=dev
npm run build

# Executar migrations
php artisan migrate --force

# Limpar e otimizar
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar queue
php artisan queue:restart

# Desativar modo manutenção
php artisan up
```

---

## 📊 Workflows Configurados

### 1. Laravel CI/CD (`laravel.yml`)
- **Trigger:** Push/PR em `main` ou `develop`
- **Ações:**
  - Build e testes
  - Laravel Pint (code style)
  - PHPStan (análise estática)

### 2. Deploy Production (`deploy.yml`)
- **Trigger:** Push em `main`
- **Ações:**
  - Build de assets
  - Deploy via SSH
  - Migrations automáticas
  - Cache e otimizações

### 3. Deploy KlerosTest (`deploy-klerostest.yml`)
- **Trigger:** Push em `teste`
- **Ações:**
  - Deploy para `/var/www/klerostest`
  - Migrations
  - Cache

---

## 🔍 Verificar Logs

### No GitHub
```
Actions > Workflow > Job > Step
```

### No Servidor
```bash
# Logs da aplicação
tail -f /var/www/kleros/storage/logs/laravel.log

# Logs do Nginx
tail -f /var/log/nginx/error.log

# Logs do PHP
tail -f /var/log/php8.2-fpm.log
```

---

## 🛡️ Melhorias de Segurança

### 1. Usar Deploy Keys ao invés de Personal Access Token
```bash
# Gerar nova chave SSH no servidor
ssh-keygen -t ed25519 -C "deploy@kleros" -f ~/.ssh/deploy_kleros

# Adicionar chave pública no GitHub:
# Settings > Deploy keys > Add deploy key
cat ~/.ssh/deploy_kleros.pub

# Adicionar chave privada nos Secrets:
# Settings > Secrets > SSH_KEY
cat ~/.ssh/deploy_kleros
```

### 2. Limitar Acesso SSH por IP
```bash
# Editar /etc/ssh/sshd_config
AllowUsers root@192.30.252.0/22 root@185.199.108.0/22

# Reiniciar SSH
sudo systemctl restart sshd
```

---

## 📞 Suporte

Em caso de problemas:
1. Verificar logs do GitHub Actions
2. Verificar logs do servidor
3. Testar conexão SSH manualmente
4. Verificar se secrets estão configurados
5. Revisar este documento

---

**Última atualização:** 18 de março de 2026  
**Status:** ✅ Workflows funcionando com timeouts otimizados
