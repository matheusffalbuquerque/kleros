# Ambiente klerostest (staging)

Este guia configura o ambiente de testes `klerostest` para receber deploy automático quando a branch `teste` for atualizada.

## 1. Estrutura no servidor

Crie o diretório do ambiente de testes:

- `/var/www/klerostest`

Copie o arquivo `.env.klerostest.example` para `/var/www/klerostest/.env` e ajuste os valores:

- `APP_URL`
- `DB_*`
- `MAIL_*`
- `REDIS_*`

## 2. Nginx

Crie um virtual host para o subdomínio do ambiente de testes (ex.: `klerostest.kleros.app`) apontando para `/var/www/klerostest/public`.

## 3. Certificado SSL

Se estiver usando wildcard (`*.kleros.app`), o cert existente cobre o subdomínio. Caso contrário, inclua `klerostest.kleros.app` no cert.

## 4. GitHub Actions (deploy automático)

O workflow `deploy-klerostest.yml` dispara quando há push na branch `teste`.

Configure os seguintes secrets no GitHub:

- `KLEROSTEST_SSH_HOST`
- `KLEROSTEST_SSH_USER`
- `KLEROSTEST_SSH_KEY`
- `KLEROSTEST_SSH_PORT`

## 5. Banco de dados

Crie um banco dedicado:

- `kleros_test`

E ajuste permissões para o usuário do ambiente de testes.

## 6. Teste rápido

Após configurar:

1. Faça push para a branch `teste`.
2. Aguarde o workflow finalizar.
3. Acesse o subdomínio de teste.
