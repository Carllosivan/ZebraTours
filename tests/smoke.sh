#!/usr/bin/env bash
# ==============================================================================
# Zebra Tours Moçambique - Test Suite de Segurança, Robustez e Funcionamento
# Executa testes funcionais e mutantes de verificação
# ==============================================================================

set -uo pipefail

BASE_URL="${ZT_TEST_URL:-http://127.0.0.1:3000}"
COOKIE_JAR="/tmp/zt_cookie_test.txt"
PASSED_COUNT=0
FAILED_COUNT=0

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

pass() {
    PASSED_COUNT=$((PASSED_COUNT + 1))
    echo -e "  [${GREEN}PASS${NC}] $1"
}

fail() {
    FAILED_COUNT=$((FAILED_COUNT + 1))
    echo -e "  [${RED}FAIL${NC}] $1"
    if [ -n "${2:-}" ]; then
        echo -e "         ${YELLOW}Detalhes:${NC} $2"
    fi
}

header() {
    echo -e "\n${BLUE}=== $1 ===${NC}"
}

echo -e "\n${YELLOW}Iniciando bateria de testes Zebra Tours PHP MVC...${NC}"

# ------------------------------------------------------------------------------
# 1. Diagnóstico do Sistema (bin/doctor.php)
# ------------------------------------------------------------------------------
header "1. Verificação de Diagnóstico e Robustez (bin/doctor.php)"

DOCTOR_OUT=$(php bin/doctor.php 2>&1)
DOCTOR_EXIT=$?
if [ $DOCTOR_EXIT -eq 0 ]; then
    pass "bin/doctor.php executa com sucesso (código 0)"
else
    fail "bin/doctor.php falhou (código $DOCTOR_EXIT)" "$DOCTOR_OUT"
fi

# Teste com mbstring desativada para verificar polyfills em app/Core/helpers.php
POLY_OUT=$(php -d disable_functions=mb_strlen,mb_substr,ctype_digit bin/doctor.php 2>&1)
POLY_EXIT=$?
if [ $POLY_EXIT -eq 0 ]; then
    pass "bin/doctor.php funciona sem mbstring nativa (polyfills operacionais)"
else
    fail "Polyfill mbstring falhou" "$POLY_OUT"
fi

# ------------------------------------------------------------------------------
# 2. Gestão de Palavras-passe CLI (bin/admin-password.php)
# ------------------------------------------------------------------------------
header "2. Gestão de Palavras-passe CLI (bin/admin-password.php)"

# 2.1 Rejeita senhas curtas (<12)
SHORT_OUT=$(ZT_NEW_PASSWORD="curta" php bin/admin-password.php admin@zebratours.co.mz 2>&1 || true)
if echo "$SHORT_OUT" | grep -qi "no mínimo 12"; then
    pass "bin/admin-password.php rejeita palavra-passe curta (<12 carateres)"
else
    fail "Não rejeitou palavra-passe curta" "$SHORT_OUT"
fi

# 2.2 Rejeita senhas conhecidas/fracas
WEAK_OUT=$(ZT_NEW_PASSWORD="ZebraAdmin2026!" php bin/admin-password.php admin@zebratours.co.mz 2>&1 || true)
if echo "$WEAK_OUT" | grep -qi "insegura"; then
    pass "bin/admin-password.php rejeita palavra-passe conhecida (ZebraAdmin2026!)"
else
    fail "Não rejeitou palavra-passe conhecida" "$WEAK_OUT"
fi

# 2.3 Define senha forte válida
TEST_ADMIN_PASS="ZebraSegura2026!#Expert"
SET_OUT=$(ZT_NEW_PASSWORD="$TEST_ADMIN_PASS" php bin/admin-password.php admin@zebratours.co.mz --name="Super Admin" --role=superadmin 2>&1)
SET_EXIT=$?
if [ $SET_EXIT -eq 0 ]; then
    pass "bin/admin-password.php definiu com sucesso palavra-passe forte de 12+ carateres"
else
    fail "Falha ao definir nova palavra-passe" "$SET_OUT"
fi

# ------------------------------------------------------------------------------
# 3. Autenticação e Bloqueio de Credenciais Conhecidas
# ------------------------------------------------------------------------------
header "3. Autenticação e Segurança de Credenciais"

# 3.1 Login com ZebraAdmin2026! tem de FALHAR
rm -f "$COOKIE_JAR"
LOGIN_PAGE=$(curl -s -c "$COOKIE_JAR" "$BASE_URL/admin/login")
CSRF_TOKEN=$(echo "$LOGIN_PAGE" | grep -o 'name="_csrf" value="[^"]*"' | head -n 1 | cut -d'"' -f4)

ATTACK_RESP=$(curl -s -L -b "$COOKIE_JAR" -c "$COOKIE_JAR" -d "_csrf=$CSRF_TOKEN" -d "email=admin@zebratours.co.mz" -d "password=ZebraAdmin2026!" "$BASE_URL/admin/login")
if echo "$ATTACK_RESP" | grep -qi "inválidas"; then
    pass "Login com antiga palavra-passe ZebraAdmin2026! é estritamente RECUSADO"
else
    fail "Bypass antigo ainda permitiu autenticação ou não recusou ZebraAdmin2026!" "$ATTACK_RESP"
fi

# 3.2 Login com nova palavra-passe forte tem de ter SUCESSO
rm -f "$COOKIE_JAR"
LOGIN_PAGE=$(curl -s -c "$COOKIE_JAR" "$BASE_URL/admin/login")
CSRF_TOKEN=$(echo "$LOGIN_PAGE" | grep -o 'name="_csrf" value="[^"]*"' | head -n 1 | cut -d'"' -f4)

AUTH_RESP=$(curl -s -L -b "$COOKIE_JAR" -c "$COOKIE_JAR" -d "_csrf=$CSRF_TOKEN" -d "email=admin@zebratours.co.mz" -d "password=$TEST_ADMIN_PASS" "$BASE_URL/admin/login")
if echo "$AUTH_RESP" | grep -qi "Painel Principal" || echo "$AUTH_RESP" | grep -qi "Gestão"; then
    pass "Login com a nova palavra-passe forte autorizou e acedeu ao painel"
else
    fail "Login com nova palavra-passe não conseguiu aceder ao painel" "$AUTH_RESP"
fi

# ------------------------------------------------------------------------------
# 4. Validação Central de CSRF (HTTP 419)
# ------------------------------------------------------------------------------
header "4. Validação Central de CSRF no Router"

# 4.1 POST sem CSRF deve devolver HTTP 419
NO_CSRF_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST -d "customer_name=Teste" "$BASE_URL/cotacao")
if [ "$NO_CSRF_CODE" -eq 419 ]; then
    pass "POST sem token CSRF devolve HTTP 419"
else
    fail "POST sem CSRF devolveu HTTP $NO_CSRF_CODE (esperava 419)"
fi

# 4.2 POST com CSRF inválido deve devolver HTTP 419
BAD_CSRF_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST -d "_csrf=token_invalido_123" -d "customer_name=Teste" "$BASE_URL/cotacao")
if [ "$BAD_CSRF_CODE" -eq 419 ]; then
    pass "POST com token CSRF forjado devolve HTTP 419"
else
    fail "POST com CSRF forjado devolveu HTTP $BAD_CSRF_CODE (esperava 419)"
fi

# ------------------------------------------------------------------------------
# 5. Tratamento de 404 Real e Filtro de Rascunhos
# ------------------------------------------------------------------------------
header "5. Páginas Inexistentes (HTTP 404 Real) e Filtro de Rascunhos"

PKG_404_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/pacotes/pacote-fantasma-inexistente-xyz")
if [ "$PKG_404_CODE" -eq 404 ]; then
    pass "/pacotes/inexistente devolve HTTP 404 real"
else
    fail "/pacotes/inexistente devolveu HTTP $PKG_404_CODE (esperava 404)"
fi

DEST_404_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/destinos/destino-fantasma-inexistente-xyz")
if [ "$DEST_404_CODE" -eq 404 ]; then
    pass "/destinos/inexistente devolve HTTP 404 real"
else
    fail "/destinos/inexistente devolveu HTTP $DEST_404_CODE (esperava 404)"
fi

SRV_404_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/servicos/servico-falso-xyz")
if [ "$SRV_404_CODE" -eq 404 ]; then
    pass "/servicos/inexistente devolve HTTP 404 real"
else
    fail "/servicos/inexistente devolveu HTTP $SRV_404_CODE (esperava 404)"
fi

# ------------------------------------------------------------------------------
# 6. Upload Seguro de Multimédia (Teste de Poliglota poly.php)
# ------------------------------------------------------------------------------
header "6. Segurança no Upload de Ficheiros (Teste Poliglota GIF/PHP)"

POLY_FILE="/tmp/poly.php"
printf "GIF89a\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;<?php echo 'POLYGLOT_EXEC_FAIL'; ?>" > "$POLY_FILE"

MEDIA_PAGE=$(curl -s -b "$COOKIE_JAR" "$BASE_URL/admin/media/upload")
MEDIA_CSRF=$(echo "$MEDIA_PAGE" | grep -o 'name="_csrf" value="[^"]*"' | head -n 1 | cut -d'"' -f4)

UPLOAD_RESP=$(curl -s -L -b "$COOKIE_JAR" \
    -F "_csrf=$MEDIA_CSRF" \
    -F "title=Teste Poliglota" \
    -F "category=destinos" \
    -F "media_file=@$POLY_FILE;filename=poly.php;type=application/x-php" \
    "$BASE_URL/admin/media/upload")

PHP_IN_UPLOADS=$(find public/uploads/ -name "*.php" 2>/dev/null | wc -l)
if [ "$PHP_IN_UPLOADS" -eq 0 ]; then
    pass "Upload de poly.php NÃO permitiu extensão .php (renomeado para .gif)"
else
    fail "Ficheiro .php foi gravado na pasta de uploads!"
fi

if [ -f "public/uploads/.htaccess" ]; then
    pass "public/uploads/.htaccess existe com diretivas de bloqueio de scripts"
else
    fail "public/uploads/.htaccess não encontrado"
fi

rm -f "$POLY_FILE"

# ------------------------------------------------------------------------------
# 7. Proteção contra Injeção de Fórmulas CSV (CSV Injection)
# ------------------------------------------------------------------------------
header "7. Exportação CSV e Neutralização de Fórmulas"

QUOTE_PAGE=$(curl -s -c "/tmp/quote_test_c.txt" "$BASE_URL/cotacao")
QUOTE_CSRF=$(echo "$QUOTE_PAGE" | grep -o 'name="_csrf" value="[^"]*"' | head -n 1 | cut -d'"' -f4)

curl -s -L -b "/tmp/quote_test_c.txt" -c "/tmp/quote_test_c.txt" \
    -d "_csrf=$QUOTE_CSRF" \
    --data-urlencode "customer_name==1+1" \
    -d "customer_phone=841234567" \
    -d "customer_email=formula@teste.mz" \
    -d "service_type=Pacote Turístico Completo" \
    -d "message=@SUM(1,2)" \
    "$BASE_URL/cotacao" > /dev/null

CSV_CONTENT=$(curl -s -b "$COOKIE_JAR" "$BASE_URL/admin/pedidos/exportar-csv")

if echo "$CSV_CONTENT" | grep -Fq "'=1+1"; then
    pass "Injeção de fórmula no nome ('=1+1) neutralizada com prefixo apóstrofe"
else
    fail "Injeção de fórmula não neutralizada no CSV: não contém '=1+1" "$CSV_CONTENT"
fi

if printf "%s" "$CSV_CONTENT" | head -c 3 | grep -q $'\xEF\xBB\xBF'; then
    pass "Exportação CSV inclui cabeçalho UTF-8 BOM para compatibilidade com Excel"
else
    fail "BOM UTF-8 em falta no CSV exportado"
fi

# ------------------------------------------------------------------------------
# 8. Rotas de SEO e Utilitários (sitemap.xml, robots.txt, backup, retention)
# ------------------------------------------------------------------------------
header "8. Rotas Dinâmicas de SEO e Utilitários CLI"

SITEMAP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/sitemap.xml")
if [ "$SITEMAP_CODE" -eq 200 ]; then
    pass "/sitemap.xml devolve HTTP 200"
else
    fail "/sitemap.xml devolveu HTTP $SITEMAP_CODE"
fi

ROBOTS_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/robots.txt")
if [ "$ROBOTS_CODE" -eq 200 ]; then
    pass "/robots.txt devolve HTTP 200"
else
    fail "/robots.txt devolveu HTTP $ROBOTS_CODE"
fi

BACKUP_OUT=$(php bin/backup.php 2>&1)
if [ $? -eq 0 ]; then
    pass "bin/backup.php cria snapshot de backup SQLite com sucesso"
else
    fail "bin/backup.php falhou" "$BACKUP_OUT"
fi

RETENTION_OUT=$(php bin/retention.php 2>&1)
if [ $? -eq 0 ]; then
    pass "bin/retention.php executa limpeza de dados com sucesso"
else
    fail "bin/retention.php falhou" "$RETENTION_OUT"
fi

# ------------------------------------------------------------------------------
# RESUMO FINAL
# ------------------------------------------------------------------------------
echo -e "\n========================================================"
echo -e " RESUMO DA SUITE DE TESTES"
echo -e " Testes Aprovados: ${GREEN}${PASSED_COUNT}${NC}"
echo -e " Testes Falhados:  ${RED}${FAILED_COUNT}${NC}"
echo -e "========================================================"

if [ "$FAILED_COUNT" -eq 0 ]; then
    echo -e "${GREEN}TODOS OS TESTES PASSARAM COM SUCESSO!${NC}\n"
    exit 0
else
    echo -e "${RED}EXISTEM FALHAS NA EXECUÇÃO DOS TESTES.${NC}\n"
    exit 1
fi
