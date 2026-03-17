# Sistema de Backup do Banco de Dados

## 📋 Descrição

Sistema automatizado de backup do banco de dados MySQL do Kleros, que:
- Gera dump completo do banco de dados
- Mantém apenas os 2 backups mais recentes
- Copia automaticamente para o ambiente de teste
- Executa diariamente às 06:00 da manhã
- Arquivos nomeados com data e hora para fácil identificação

## 📁 Estrutura de Arquivos

```
storage/backups/database/
├── kleros_backup_2026-03-17_06-00-00.sql
└── kleros_backup_2026-03-16_06-00-00.sql

/var/www/klerostest/
└── kleros_dump_2026-03-17.sql
```

## 🚀 Comandos Disponíveis

### Executar backup manualmente
```bash
php artisan db:backup
```

### Testar o backup
```bash
php artisan db:backup --test
```

### Ver logs de backup
```bash
tail -f storage/logs/laravel.log | grep -i backup
```

## ⏰ Agendamento

O backup é executado automaticamente através do Laravel Scheduler:
- **Horário:** 06:00 da manhã (todos os dias)
- **Job:** `DatabaseBackupJob`
- **Comando:** `db:backup`

Para verificar os agendamentos:
```bash
php artisan schedule:list
```

## 🔄 Restaurar Backup

### No ambiente de teste (klerostest)
```bash
cd /var/www/klerostest
mysql -u kleros_user -p kleros_test_db < kleros_dump_2026-03-17.sql
```

### No ambiente local (desenvolvimento)
```bash
mysql -u root -p kleros_local < /var/www/kleros/storage/backups/database/kleros_backup_2026-03-17_06-00-00.sql
```

## 📊 Informações Técnicas

### O que é incluído no dump?
- ✅ Estrutura de todas as tabelas
- ✅ Todos os dados (úteis para teste e desenvolvimento)
- ✅ Índices e constraints
- ✅ Triggers e stored procedures

### O que NÃO é incluído?
- ❌ Senhas em texto puro (são hasheadas)
- ❌ Tokens de API sensíveis (devem ser reconfigurados)

### Segurança
- Backups armazenados em `storage/backups/database` (fora do webroot)
- Apenas 2 arquivos mantidos para economizar espaço
- Permissões restritas (0755)

## 🛠️ Manutenção

### Verificar espaço em disco
```bash
du -sh /var/www/kleros/storage/backups/database
```

### Limpar backups manualmente (se necessário)
```bash
rm /var/www/kleros/storage/backups/database/kleros_backup_*.sql
```

### Alterar horário do backup
Edite o arquivo `routes/console.php`:
```php
Schedule::job(new DatabaseBackupJob())->dailyAt('06:00'); // Altere o horário aqui
```

## 🐛 Troubleshooting

### Backup não está sendo criado?
1. Verifique se o cron está rodando: `crontab -l`
2. Verifique os logs: `tail -f storage/logs/laravel.log`
3. Teste manualmente: `php artisan db:backup`

### Erro de permissão?
```bash
sudo chmod -R 755 /var/www/kleros/storage/backups
sudo chown -R www-data:www-data /var/www/kleros/storage/backups
```

### Arquivo muito grande?
Considere comprimir os backups:
```bash
gzip /var/www/kleros/storage/backups/database/*.sql
```

## 📝 Logs

Todos os backups são registrados em:
- `storage/logs/laravel.log` - Logs da aplicação
- Saída do comando `db:backup` - Informações em tempo real

## ⚠️ Avisos Importantes

1. **Ambiente de produção:** Os backups contêm dados reais
2. **Ambiente de teste:** Sempre sanitize dados sensíveis antes de usar
3. **Espaço em disco:** Monitore regularmente o tamanho dos backups
4. **Restauração:** Sempre faça backup antes de restaurar
5. **Cron:** Certifique-se de que o scheduler está rodando (`* * * * * cd /var/www/kleros && php artisan schedule:run`)
