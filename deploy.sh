#!/bin/bash

###############################################################################
# Script de Deploy Manual - Kleros Production
###############################################################################
# 
# Uso:
#   ./deploy.sh
#
###############################################################################

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  Deploy Kleros - Produção${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo ""

# Verificar se está no diretório correto
if [ ! -f "artisan" ]; then
    echo -e "${RED} Erro: Este script deve ser executado no diretório raiz do projeto Kleros${NC}"
    exit 1
fi

# Confirmação
echo -e "${YELLOW}  Este script irá:${NC}"
echo "   1. Ativar modo manutenção"
echo "   2. Atualizar código (git pull)"
echo "   3. Instalar dependências (Composer + NPM)"
echo "   4. Compilar assets (Vite)"
echo "   5. Executar migrations"
echo "   6. Limpar e otimizar cache"
echo "   7. Reiniciar queue workers"
echo "   8. Desativar modo manutenção"
echo ""
read -p "Deseja continuar? (s/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo -e "${YELLOW}  Deploy cancelado${NC}"
    exit 0
fi

echo ""
echo -e "${YELLOW}🔄 Ativando modo manutenção...${NC}"
php artisan down || true

echo ""
echo -e "${YELLOW} Atualizando código do repositório...${NC}"
git fetch origin main
BEFORE=$(git rev-parse HEAD)
git pull origin main
AFTER=$(git rev-parse HEAD)

if [ "$BEFORE" == "$AFTER" ]; then
    echo -e "${GREEN} Código já está atualizado (nenhum commit novo)${NC}"
else
    echo -e "${GREEN} Código atualizado: ${BEFORE:0:7} → ${AFTER:0:7}${NC}"
fi

echo ""
echo -e "${YELLOW}📦 Instalando dependências do Composer...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -eq 0 ]; then
    echo -e "${GREEN} Dependências do Composer instaladas${NC}"
else
    echo -e "${RED} Erro ao instalar dependências do Composer${NC}"
    php artisan up
    exit 1
fi

echo ""
echo -e "${YELLOW} Instalando dependências do NPM...${NC}"
npm install --omit=dev
if [ $? -eq 0 ]; then
    echo -e "${GREEN} Dependências do NPM instaladas${NC}"
else
    echo -e "${RED} Erro ao instalar dependências do NPM${NC}"
    php artisan up
    exit 1
fi

echo ""
echo -e "${YELLOW} Compilando assets (Vite)...${NC}"
npm run build
if [ $? -eq 0 ]; then
    echo -e "${GREEN} Assets compilados com sucesso${NC}"
    ls -lh public/build/manifest.json 2>/dev/null
else
    echo -e "${RED} Erro ao compilar assets${NC}"
    php artisan up
    exit 1
fi

echo ""
echo -e "${YELLOW}  Executando migrations...${NC}"
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN} Migrations executadas${NC}"
else
    echo -e "${RED} Erro ao executar migrations${NC}"
    php artisan up
    exit 1
fi

echo ""
echo -e "${YELLOW} Limpando cache...${NC}"
php artisan optimize:clear
echo -e "${GREEN} Cache limpo${NC}"

echo ""
echo -e "${YELLOW} Otimizando aplicação...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN} Aplicação otimizada${NC}"

echo ""
echo -e "${YELLOW} Reiniciando queue workers...${NC}"
php artisan queue:restart
echo -e "${GREEN} Queue workers reiniciados${NC}"

echo ""
echo -e "${YELLOW} Desativando modo manutenção...${NC}"
php artisan up
echo -e "${GREEN} Aplicação online${NC}"

echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  Deploy Concluído com Sucesso!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${BLUE} Informações:${NC}"
echo "   • Commit: $(git log -1 --pretty=format:'%h - %s')"
echo "   • Data: $(date '+%d/%m/%Y %H:%M:%S')"
echo "   • Usuário: $(whoami)"
echo ""
echo -e "${YELLOW} Próximos passos:${NC}"
echo "   1. Verificar aplicação: https://kleros.app"
echo "   2. Verificar logs: tail -f storage/logs/laravel.log"
echo "   3. Monitorar queue: php artisan queue:monitor default"
echo ""
