#!/usr/bin/env bash

# ==============================================================================
# Script de Instalação e Onboarding Automatizado
# Portal Life Church - Sistema de Gestão Eclesiástica (Edificar)
# ==============================================================================

set -e

RED='\030[0;31m'
GREEN='\033[0;32m'
ORANGE='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${ORANGE}"
echo "======================================================================"
echo "    LIFE CHURCH - INSTALADOR DE NOVA CONGREGAÇÃO / CAMPUS (EDIFICAR)  "
echo "======================================================================"
echo -e "${NC}"

# 1. Verificar PHP
echo -e "${BLUE}[1/6] A verificar requisitos do sistema...${NC}"
if ! command -v php &> /dev/null; then
    echo -e "${RED}Erro: PHP não foi encontrado no sistema. Por favor, instale o PHP 8.2 ou superior.${NC}"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo -e "${GREEN}✓ PHP Detetado: ${PHP_VERSION}${NC}"

# 2. Configuração do Ficheiro .env
echo -e "${BLUE}[2/6] A verificar ficheiro de configuração (.env)...${NC}"
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo -e "${GREEN}✓ Ficheiro .env criado a partir de .env.example${NC}"
    else
        echo -e "${RED}Erro: .env.example não foi encontrado.${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}✓ Ficheiro .env existente reutilizado.${NC}"
fi

# 3. Instalação de Dependências PHP
echo -e "${BLUE}[3/6] A verificar dependências (Composer)...${NC}"
if [ ! -d "vendor" ]; then
    if command -v composer &> /dev/null; then
        composer install --no-dev --optimize-autoloader
        echo -e "${GREEN}✓ Dependências instaladas com sucesso.${NC}"
    else
        echo -e "${ORANGE}Aviso: Composer não encontrado globalmente. Ignorando se vendor já existir.${NC}"
    fi
else
    echo -e "${GREEN}✓ Pasta vendor detetada.${NC}"
fi

# 4. Chave da Aplicação & Storage Link
echo -e "${BLUE}[4/6] A gerar chave da aplicação e ligações de armazenamento...${NC}"
php artisan key:generate --force
php artisan storage:link --force || true
echo -e "${GREEN}✓ Chave da aplicação gerada e storage linked.${NC}"

# 5. Migrações da Base de Dados
echo -e "${BLUE}[5/6] A executar migrações da base de dados...${NC}"
php artisan migrate --force
echo -e "${GREEN}✓ Tabelas da base de dados criadas com sucesso.${NC}"

# 6. Limpeza e Otimização de Caches
echo -e "${BLUE}[6/6] A otimizar caches do sistema...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo -e "${GREEN}✓ Caches limpas e otimizadas.${NC}"

echo ""
echo -e "${GREEN}======================================================================"
echo " 🎉 INSTALAÇÃO BASE CONCLUÍDA COM SUCESSO!"
echo "======================================================================"
echo -e "${NC}"
echo -e "Para concluir a configuração da sua congregação, abra o seu browser e aceda:"
echo -e "👉 ${ORANGE}http://seu-dominio-ou-ip/setup${NC}  (ou /edificar/setup)"
echo -e "Siga o assistente visual para definir o nome da igreja, logótipo e a conta do Pastor Admin."
echo ""
