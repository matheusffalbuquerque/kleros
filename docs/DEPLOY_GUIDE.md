# 🚀 Guia de Deploy - Kleros

## 📋 Métodos de Deploy

### 1. Deploy Manual com Script (Recomendado)

O método mais simples e confiável para deploy manual no servidor.

```bash
cd /var/www/kleros
./deploy.sh
```

**O que o script faz:**
1. ✅ Ativa modo manutenção
2. ✅ Faz git pull da branch main
3. ✅ Instala dependências PHP (Composer)
4. ✅ Instala dependências JS (NPM)
5. ✅ **Compila assets (Vite)** ← IMPORTANTE
6. ✅ Executa migrations
7. ✅ Limpa e otimiza cache
8. ✅ Reinicia queue workers
9. ✅ Desativa modo manutenção

---

### 2. Deploy Manual Passo a Passo

Se preferir fazer manualmente ou debugar algum problema:

```bash
cd /var/www/kleros

# 1. Modo manutenção
php artisan down

# 2. Atualizar código
git pull origin main

# 3. Instalar dependências PHP
composer install --no-dev --optimize-autoloader

# 4. Instalar dependências JS
npm install --omit=dev

# 5. Compilar assets ⚠️ NÃO ESQUEÇA ESTE PASSO!
npm run build

# 6. Migrations
php artisan migrate --force

# 7. Limpar cache
php artisan optimize:clear

# 8. Otimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Reiniciar queue
php artisan queue:restart

# 10. Desativar manutenção
php artisan up
```

---

### 3. Deploy Automático via GitHub Actions

**Status:** ⚠️ Configurado mas com problemas de SSH timeout

Os workflows estão em `.github/workflows/`:
- `deploy.yml` - Deploy automático ao fazer push na `main`
- `laravel.yml` - CI/CD (testes, linting)

**Para ativar:**
1. Configurar secrets no GitHub (já configurados)
2. Push para a branch `main`
3. GitHub Actions executa automaticamente

**Troubleshooting:** Ver `docs/GITHUB_ACTIONS_TROUBLESHOOTING.md`

---

## ⚠️ IMPORTANTE: Compilação de Assets

### Por que preciso compilar assets?

O Laravel Vite compila e otimiza os arquivos JavaScript e CSS:
- ❌ **Sem compilar:** Mudanças no JS/CSS não aparecem
- ✅ **Compilado:** Assets otimizados e minificados

### Quando compilar?

**Sempre que houver mudanças em:**
- `resources/js/**/*.js`
- `resources/css/**/*.css` ou `.scss`
- `resources/views/**/*.blade.php` (se usar Alpine.js inline)

### Como saber se preciso compilar?

```bash
# Ver quando foi a última compilação
ls -lh /var/www/kleros/public/build/manifest.json

# Se a data for antiga, compile novamente:
npm run build
```

### Sinais de que esqueceu de compilar:

- ✗ Mudanças no JavaScript não aparecem
- ✗ Estilos CSS não atualizaram
- ✗ Console do navegador mostra erros 404 em assets
- ✗ Manifest.json com data antiga

---

## 📊 Verificação Pós-Deploy

### 1. Verificar aplicação
```bash
curl -I https://kleros.app
# Deve retornar HTTP/2 200
```

### 2. Verificar assets compilados
```bash
ls -lah /var/www/kleros/public/build/assets/
# Deve mostrar arquivos com data/hora recente
```

### 3. Verificar logs
```bash
tail -f /var/www/kleros/storage/logs/laravel.log
```

### 4. Verificar queue
```bash
# Ver se worker está rodando
ps aux | grep "queue:work"

# Ver status
php artisan queue:monitor default
```

### 5. Verificar cache
```bash
# Deve mostrar configurações cacheadas
php artisan config:show

# Ver rotas cacheadas
php artisan route:list | head
```

---

## 🐛 Problemas Comuns

### Assets não atualizam após deploy

**Causa:** Esqueceu de compilar com `npm run build`

**Solução:**
```bash
cd /var/www/kleros
npm install
npm run build
php artisan optimize:clear
```

### Erro "vite: not found"

**Causa:** `node_modules` não instalado

**Solução:**
```bash
cd /var/www/kleros
npm install
npm run build
```

### Erro de permissão

**Solução:**
```bash
sudo chown -R www-data:www-data /var/www/kleros
sudo chmod -R 755 /var/www/kleros
sudo chmod -R 775 /var/www/kleros/storage
sudo chmod -R 775 /var/www/kleros/bootstrap/cache
```

### Modo manutenção travado

**Solução:**
```bash
php artisan up
# ou
rm storage/framework/down
```

### Cache não atualiza

**Solução:**
```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 📝 Checklist de Deploy

Antes de fazer deploy:
- [ ] Código testado localmente
- [ ] Migrations testadas
- [ ] Assets compilados (`npm run build`)
- [ ] Commit e push feitos
- [ ] Backup do banco (automático às 06:00)

Durante o deploy:
- [ ] Executar `./deploy.sh` ou seguir passos manuais
- [ ] Aguardar conclusão sem erros
- [ ] Verificar aplicação online

Após o deploy:
- [ ] Testar funcionalidades principais
- [ ] Verificar assets carregando
- [ ] Verificar logs sem erros
- [ ] Queue processando jobs
- [ ] Notificar equipe se necessário

---

## 🔄 Rollback (Reverter Deploy)

Se algo der errado:

```bash
cd /var/www/kleros

# 1. Modo manutenção
php artisan down

# 2. Voltar para commit anterior
git log --oneline -5
git reset --hard COMMIT_HASH_ANTERIOR

# 3. Reinstalar dependências
composer install --no-dev --optimize-autoloader
npm install --omit=dev
npm run build

# 4. Limpar cache
php artisan optimize:clear
php artisan config:cache

# 5. Desativar manutenção
php artisan up
```

---

## 📞 Suporte

**Documentação relacionada:**
- `docs/BACKUP_SYSTEM.md` - Sistema de backup
- `docs/GITHUB_ACTIONS_TROUBLESHOOTING.md` - CI/CD
- `BACKUP_SUMMARY.md` - Resumo de backups

**Logs importantes:**
- Laravel: `storage/logs/laravel.log`
- Nginx: `/var/log/nginx/error.log`
- PHP-FPM: `/var/log/php8.2-fpm.log`

---

**Última atualização:** 18 de março de 2026  
**Versão:** 1.0
