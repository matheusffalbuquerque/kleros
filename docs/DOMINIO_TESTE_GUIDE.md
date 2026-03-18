# 🌐 GUIA - Configuração de Domínios Multi-tenant para Testes

## 📋 Resumo

O Kleros usa arquitetura **multi-tenant baseada em domínios**, onde cada congregação tem seu próprio domínio/subdomínio registrado no banco de dados.

---

## ✅ Configuração Atual (Staging/Teste)

### **Domínios do Sistema:**
- **Site Principal**: `klerostest.kleros.app`
- **Painel Admin**: `admin-teste.kleros.app`
- **Congregações**: `{congregacao}.kleros.app`

### **Variáveis no .env:**
```properties
APP_ENV=staging
APP_URL=https://klerostest.kleros.app

# Domínios personalizados para staging
APP_DOMAIN_PUBLIC=klerostest.kleros.app
APP_DOMAIN_ADMIN=admin-teste.kleros.app
APP_DOMAIN_SCHEME=https
```

---

## 🏗️ Como Funciona o Multi-tenancy

### **Fluxo de Requisição:**

```
1. Usuário acessa: agapehouseisateste.kleros.app
   ↓
2. Middleware: AcessarCongregacaoPeloDominio
   ↓
3. Busca na tabela 'dominios' WHERE dominio = 'agapehouseisateste.kleros.app'
   ↓
4. Carrega a congregação associada
   ↓
5. Disponibiliza via: app('congregacao')
```

### **Importante:**
- O middleware compara o **HOST COMPLETO** (não apenas o subdomínio)
- Deve haver correspondência EXATA entre o domínio acessado e o registro no banco

---

## 🔧 Como Cadastrar Domínios de Teste

### **Opção 1: Via Banco de Dados (MySQL)**

```sql
-- Ver congregações existentes
SELECT id, identificacao FROM congregacoes;

-- Cadastrar novo domínio
INSERT INTO dominios (congregacao_id, dominio, ativo, created_at, updated_at)
VALUES (
    2,  -- ID da congregação
    'agapehouseisateste.kleros.app',  -- Domínio COMPLETO
    1,  -- Ativo (1 = sim, 0 = não)
    NOW(),
    NOW()
);

-- Verificar domínios cadastrados
SELECT d.id, d.dominio, d.ativo, c.identificacao as congregacao
FROM dominios d
LEFT JOIN congregacoes c ON d.congregacao_id = c.id
ORDER BY d.id DESC;
```

### **Opção 2: Via Tinker (Laravel)**

```bash
cd /var/www/klerostest
php artisan tinker
```

```php
// Listar congregações
\App\Models\Congregacao::select('id', 'identificacao')->get();

// Criar novo domínio
\App\Models\Dominio::create([
    'congregacao_id' => 2,
    'dominio' => 'agapehouseisateste.kleros.app',
    'ativo' => true
]);

// Listar todos os domínios
\App\Models\Dominio::with('congregacao')->get();
```

---

## 🧪 Como Testar

### **1. Verificar Registro no Banco:**
```bash
mysql -u kleros_user -p'Kleros135@admin' klerostest_db -e "
SELECT d.id, d.dominio, d.ativo, c.identificacao 
FROM dominios d 
LEFT JOIN congregacoes c ON d.congregacao_id = c.id 
WHERE d.dominio = 'agapehouseisateste.kleros.app';
"
```

### **2. Limpar Todos os Caches:**
```bash
cd /var/www/klerostest
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan config:cache
```

### **3. Testar via Rota de Debug:**
```bash
# Acesse no navegador ou via curl:
curl https://agapehouseisateste.kleros.app/test-domain

# Retorno esperado (JSON):
{
  "request_host": "agapehouseisateste.kleros.app",
  "public_domain": "klerostest.kleros.app",
  "admin_domain": "admin-teste.kleros.app",
  "is_public": false,
  "dominio_found": true,
  "dominio_data": {
    "id": 2,
    "dominio": "agapehouseisateste.kleros.app",
    "ativo": 1,
    "congregacao_id": 2,
    "congregacao": "Ilha Solteira"
  }
}
```

---

## ⚠️ Checklist de Problemas Comuns

### **Problema: Domínio cadastrado mas não funciona**

✅ **Verificações:**

1. **DNS está apontando corretamente?**
   ```bash
   nslookup agapehouseisateste.kleros.app
   # Deve retornar o IP do servidor
   ```

2. **Domínio cadastrado com nome EXATO?**
   ```sql
   -- Verificar grafia exata
   SELECT dominio FROM dominios WHERE congregacao_id = 2;
   ```

3. **Domínio está ativo?**
   ```sql
   SELECT ativo FROM dominios WHERE dominio = 'agapehouseisateste.kleros.app';
   -- Deve retornar: 1
   ```

4. **Cache foi limpo?**
   ```bash
   php artisan config:clear && php artisan config:cache
   ```

5. **Servidor web (Nginx/Apache) está configurado para aceitar o domínio?**
   - Verificar se há wildcard: `*.kleros.app`
   - Ou se o domínio específico está no `server_name`

6. **HTTPS está funcionando?**
   - Certificado SSL válido para `*.kleros.app` ou domínio específico

7. **Redis não estava rodando?** ✅ JÁ CORRIGIDO
   - Mudamos para `CACHE_DRIVER=database`

---

## 📊 Estrutura do Banco de Dados

### **Tabela: dominios**
```sql
CREATE TABLE `dominios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `congregacao_id` bigint unsigned NOT NULL,
  `dominio` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dominios_congregacao_id_foreign` (`congregacao_id`),
  CONSTRAINT `dominios_congregacao_id_foreign` 
    FOREIGN KEY (`congregacao_id`) REFERENCES `congregacoes` (`id`)
) ENGINE=InnoDB;
```

---

## 🚀 Ambiente de Produção vs Staging

### **Development (local):**
```properties
APP_ENV=local
# Usa: kleros.local, admin.local
# Domínios: congregacao.local
```

### **Staging (teste):**
```properties
APP_ENV=staging
APP_DOMAIN_PUBLIC=klerostest.kleros.app
APP_DOMAIN_ADMIN=admin-teste.kleros.app
# Domínios: congregacao-teste.kleros.app
```

### **Production:**
```properties
APP_ENV=production
# Usa automaticamente: kleros.app, admin.kleros.app
# Domínios: congregacao.kleros.app
```

---

## 📝 Exemplos de Domínios de Teste

```sql
-- Padrão recomendado para staging:
INSERT INTO dominios (congregacao_id, dominio, ativo, created_at, updated_at) VALUES
(1, 'agapehouse-teste.kleros.app', 1, NOW(), NOW()),
(2, 'primeirabatista-teste.kleros.app', 1, NOW(), NOW()),
(3, 'assembleia-teste.kleros.app', 1, NOW(), NOW());

-- OU com sufixo mais descritivo:
(1, 'agapehouseisateste.kleros.app', 1, NOW(), NOW()),
(2, 'jerusalemilhateste.kleros.app', 1, NOW(), NOW());
```

**Ambos os formatos estão corretos!** O importante é:
- ✅ Usar `.kleros.app` no final
- ✅ Manter consistência no padrão escolhido
- ✅ Cadastrar o domínio COMPLETO no banco

---

## 🔐 Segurança

- ⚠️ **REMOVER** a rota `/test-domain` em produção
- ✅ Sempre validar `congregacao_id` nas queries
- ✅ Usar middleware `dominio` em todas as rotas de tenant
- ✅ Validar que usuário pertence à congregação do domínio

---

## 📞 Comandos Úteis

```bash
# Ver configuração atual
php artisan about

# Limpar todos os caches
php artisan optimize:clear

# Recriar todos os caches
php artisan optimize

# Ver rotas cadastradas
php artisan route:list | grep -i dominio

# Ver log em tempo real
tail -f storage/logs/laravel.log

# Testar conexão com banco
php artisan tinker --execute="DB::connection()->getPdo();"
```

---

## ✨ Status Atual

✅ Dependências instaladas
✅ APP_KEY gerada
✅ Banco de dados conectado
✅ Migrações executadas
✅ Assets compilados
✅ Cache configurado (database)
✅ Queue configurado (database)
✅ Domínios principais configurados
✅ Domínio `agapehouseisateste.kleros.app` cadastrado no banco

**Aplicação pronta para testes!** 🎉

---

**Data:** 17 de Março de 2026
**Ambiente:** Staging (klerostest.kleros.app)
**Versão Laravel:** 11.46.1
**PHP:** 8.2.29
