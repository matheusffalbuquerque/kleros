#!/bin/bash

###############################################################################
# Script de Restauração de Backup - Ambiente de Teste Kleros
###############################################################################
# 
# Uso:
#   ./restore-backup-test.sh                  # Usa o dump mais recente
#   ./restore-backup-test.sh 2026-03-17       # Usa dump de data específica
#
###############################################################################

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Configurações
TEST_DIR="/var/www/klerostest"
DB_NAME="kleros_test_db"
DB_USER="kleros_user"
DB_PASS="Kleros135@admin"

echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  Restauração de Backup - Ambiente de Teste Kleros${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo ""

# Verificar se data foi fornecida
if [ -z "$1" ]; then
    # Usar dump mais recente
    DUMP_FILE=$(ls -t ${TEST_DIR}/kleros_dump_*.sql 2>/dev/null | head -1)
    if [ -z "$DUMP_FILE" ]; then
        echo -e "${RED}❌ Nenhum arquivo de dump encontrado em ${TEST_DIR}${NC}"
        exit 1
    fi
else
    # Usar dump de data específica
    DUMP_FILE="${TEST_DIR}/kleros_dump_${1}.sql"
    if [ ! -f "$DUMP_FILE" ]; then
        echo -e "${RED}❌ Arquivo não encontrado: ${DUMP_FILE}${NC}"
        echo -e "${YELLOW}Dumps disponíveis:${NC}"
        ls -lh ${TEST_DIR}/kleros_dump_*.sql 2>/dev/null
        exit 1
    fi
fi

echo -e "${YELLOW}📁 Arquivo de dump:${NC} $(basename $DUMP_FILE)"
echo -e "${YELLOW}📊 Tamanho:${NC} $(du -h $DUMP_FILE | cut -f1)"
echo -e "${YELLOW}💾 Banco de dados:${NC} ${DB_NAME}"
echo ""

# Confirmação
read -p "Deseja continuar com a restauração? (s/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo -e "${YELLOW}⚠️  Restauração cancelada${NC}"
    exit 0
fi

# Fazer backup do banco atual (segurança)
echo ""
echo -e "${YELLOW}🔄 Fazendo backup de segurança do banco atual...${NC}"
BACKUP_SAFETY="${TEST_DIR}/kleros_backup_before_restore_$(date +%Y-%m-%d_%H-%M-%S).sql"
mysqldump -u ${DB_USER} -p${DB_PASS} ${DB_NAME} > ${BACKUP_SAFETY} 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Backup de segurança criado: $(basename $BACKUP_SAFETY)${NC}"
else
    echo -e "${YELLOW}⚠️  Não foi possível criar backup de segurança${NC}"
fi

# Restaurar dump
echo ""
echo -e "${YELLOW}🔄 Restaurando banco de dados...${NC}"
mysql -u ${DB_USER} -p${DB_PASS} ${DB_NAME} < ${DUMP_FILE} 2>&1

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Banco de dados restaurado com sucesso!${NC}"
    
    # Limpar cache do Laravel
    if [ -d "${TEST_DIR}" ]; then
        echo ""
        echo -e "${YELLOW}🧹 Limpando cache da aplicação...${NC}"
        cd ${TEST_DIR}
        php artisan config:clear > /dev/null 2>&1
        php artisan cache:clear > /dev/null 2>&1
        php artisan route:clear > /dev/null 2>&1
        php artisan view:clear > /dev/null 2>&1
        echo -e "${GREEN}✅ Cache limpo${NC}"
    fi
    
    echo ""
    echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}  ✅ Restauração Concluída com Sucesso!${NC}"
    echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
else
    echo -e "${RED}❌ Erro ao restaurar banco de dados${NC}"
    
    # Restaurar backup de segurança
    if [ -f "$BACKUP_SAFETY" ]; then
        echo -e "${YELLOW}🔄 Restaurando backup de segurança...${NC}"
        mysql -u ${DB_USER} -p${DB_PASS} ${DB_NAME} < ${BACKUP_SAFETY}
        echo -e "${GREEN}✅ Banco revertido para estado anterior${NC}"
    fi
    
    exit 1
fi

echo ""
echo -e "${YELLOW}📋 Próximos passos:${NC}"
echo "   1. Verificar a aplicação: http://klerostest.kleros.app"
echo "   2. Testar funcionalidades críticas"
echo "   3. Verificar logs: tail -f ${TEST_DIR}/storage/logs/laravel.log"
echo ""
