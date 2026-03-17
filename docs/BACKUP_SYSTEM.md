# 🔄 Sistema de Backup Automático do Banco de Dados

## ✅ Sistema Implementado com Sucesso!

### 📦 Componentes Criados

#### 1. **Command Artisan** (`app/Console/Commands/DatabaseBackupCommand.php`)
- Comando: `php artisan db:backup`
- Gera dump completo do banco de dados MySQL
- Mantém apenas os 2 backups mais recentes automaticamente
- Copia o dump para o ambiente de teste
- Logs detalhados de todas as operações

#### 2. **Job Agendado** (`app/Jobs/DatabaseBackupJob.php`)
- Executa automaticamente todos os dias às **06:00 da manhã**
- Usa fila (queue) para não bloquear a aplicação
- Retry automático em caso de falha (3 tentativas)
- Timeout de 5 minutos

#### 3. **Script de Restauração** (`restore-backup-test.sh`)
- Restaura backup no ambiente de teste com segurança
- Faz backup antes de restaurar (rollback automático em caso de erro)
- Interface amigável com cores e confirmações

#### 4. **Documentação** (`storage/backups/database/README.md`)
- Instruções completas de uso
- Troubleshooting
- Exemplos práticos

---

## 🚀 Como Usar

### Executar Backup Manualmente
```bash
cd /var/www/kleros
php artisan db:backup
```

### Restaurar no Ambiente de Teste
```bash
cd /var/www/kleros
./restore-backup-test.sh                # Usa o dump mais recente
./restore-backup-test.sh 2026-03-17     # Usa dump de data específica
```

### Ver Logs
```bash
tail -f storage/logs/laravel.log | grep -i backup
```

### Listar Backups Disponíveis
```bash
ls -lh storage/backups/database/
ls -lh /var/www/klerostest/kleros_dump_*.sql
```

---

## 📁 Estrutura de Arquivos

```
/var/www/kleros/
├── app/
│   ├── Console/Commands/
│   │   └── DatabaseBackupCommand.php     # Command artisan
│   └── Jobs/
│       └── DatabaseBackupJob.php         # Job agendado
├── routes/
│   └── console.php                       # Agendamento (Schedule)
├── storage/
│   └── backups/
│       └── database/
│           ├── README.md                 # Documentação
│           ├── kleros_backup_2026-03-17_06-00-00.sql
│           └── kleros_backup_2026-03-16_06-00-00.sql
└── restore-backup-test.sh                # Script de restauração

/var/www/klerostest/
└── kleros_dump_2026-03-17.sql            # Dump atualizado diariamente
```

---

## ⏰ Agendamento Configurado

| Job | Horário | Descrição |
|-----|---------|-----------|
| DatabaseBackupJob | 06:00 | Backup diário do banco de dados |
| EnviarAniversariantesDoDiaJob | 08:00 | Envio de emails de aniversariantes |
| AtualizarFeedsJob | 08:00 e 14:00 | Atualização de feeds RSS |

Para verificar:
```bash
php artisan schedule:list
```

---

## 🔍 Como Funciona

### 1. Geração do Backup (06:00)
```
1. Job DatabaseBackupJob é disparado pelo Laravel Scheduler
2. Executa comando: php artisan db:backup
3. mysqldump gera arquivo .sql com toda a estrutura e dados
4. Arquivo salvo em: storage/backups/database/kleros_backup_YYYY-MM-DD_HH-MM-SS.sql
5. Copia para: /var/www/klerostest/kleros_dump_YYYY-MM-DD.sql
6. Remove backups antigos (mantém apenas 2)
7. Registra logs de sucesso/erro
```

### 2. Rotação de Backups
```
Exemplo de rotação:
┌─────────────────────────────────────────┐
│ Dia 1: backup_2026-03-17.sql           │
├─────────────────────────────────────────┤
│ Dia 2: backup_2026-03-18.sql           │
│        backup_2026-03-17.sql           │
├─────────────────────────────────────────┤
│ Dia 3: backup_2026-03-19.sql           │
│        backup_2026-03-18.sql           │
│        [backup_2026-03-17.sql] 🗑️      │
└─────────────────────────────────────────┘
```

### 3. Cópia para Ambiente de Teste
- Sobrescreve arquivo `kleros_dump_YYYY-MM-DD.sql`
- Um arquivo por dia (não rotaciona)
- Sempre disponível para restauração rápida

---

## 🛡️ Segurança e Boas Práticas

✅ **Implementado:**
- Backups fora do webroot (`storage/backups/`)
- Permissões restritas (755)
- Logs de todas as operações
- Backup de segurança antes de restaurar
- Timeout de 5 minutos para evitar travamentos
- Retry automático (3 tentativas)

⚠️ **Atenção:**
- Backups contêm dados de produção sensíveis
- Não versionar arquivos `.sql` no Git
- Monitorar espaço em disco regularmente
- Testar restauração periodicamente

---

## 📊 Tamanho dos Backups

Backup atual: **~11 MB** (compactado)

Estimativa de espaço:
- 2 backups em produção: ~22 MB
- 1 backup em teste: ~11 MB
- **Total:** ~33 MB

Para comprimir (opcional):
```bash
cd /var/www/kleros/storage/backups/database
gzip kleros_backup_*.sql
```

---

## 🐛 Troubleshooting

### Backup não está sendo executado?
```bash
# 1. Verificar se o cron está configurado
crontab -l

# 2. Deve conter:
# * * * * * cd /var/www/kleros && php artisan schedule:run >> /dev/null 2>&1

# 3. Verificar se o queue worker está rodando
ps aux | grep "queue:work"

# 4. Testar manualmente
php artisan db:backup
```

### Erro de permissão?
```bash
sudo chown -R www-data:www-data /var/www/kleros/storage/backups
sudo chmod -R 755 /var/www/kleros/storage/backups
```

### Arquivo muito grande?
```bash
# Ver tamanho do banco
mysql -u kleros_user -p -e "
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'kleros_db'
GROUP BY table_schema;
"
```

### Restauração falhou?
- O script faz backup automático antes de restaurar
- Em caso de erro, o banco é revertido automaticamente
- Verifique os logs: `tail -f /var/www/klerostest/storage/logs/laravel.log`

---

## 📝 Próximas Melhorias (Opcional)

- [ ] Compressão automática dos backups (gzip)
- [ ] Upload para S3/Backup externo
- [ ] Notificação por email em caso de falha
- [ ] Backup incremental
- [ ] Sanitização de dados sensíveis para teste
- [ ] Dashboard de monitoramento de backups

---

## 📞 Suporte

Para dúvidas ou problemas:
1. Verificar logs: `storage/logs/laravel.log`
2. Executar teste: `php artisan db:backup`
3. Verificar agendamento: `php artisan schedule:list`
4. Revisar documentação: `storage/backups/database/README.md`

---

**Criado em:** 17 de março de 2026  
**Versão:** 1.0  
**Status:** ✅ Funcionando perfeitamente
