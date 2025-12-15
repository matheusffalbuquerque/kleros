# PWA da plataforma Kleros

Esta página resume como o PWA está estruturado e como a lógica de cache/atualização funciona.

## Visão geral
- Manifesto dinâmico em `GET /manifest.json` gerado por `App\Http\Controllers\PwaController`.
- Service worker em `public/service-worker.js`, registrado no layout principal (`resources/views/layouts/main.blade.php`).
- Página offline amigável em `resources/views/offline.blade.php`, servida em `GET /offline`.
- Link de instalação no dropdown do perfil (`data-pwa-install`), disparando o prompt nativo quando disponível.

## Manifesto (`/manifest.json`)
- `name` e `short_name` usam o nome curto da congregação; o `name` inclui o nome do app.
- Cores derivadas do tema da congregação (`primaria` e `cor-fundo`).
- Ícone pega o logo da congregação (`config->logo_caminho`); cai para `/favicon.ico` se ausente.
- Atalhos incluem “Abrir painel” para a raiz.
- Cabeçalho `Content-Type: application/manifest+json`.

## Service Worker (`public/service-worker.js`)
- Versão em `APP_VERSION`; usada no nome do cache estático (`static-${APP_VERSION}`).
- Pré-cache mínimo: `/`, `/manifest.json` e `/offline`.
- Estratégias:
  - Navegação (páginas): `networkFirstWithOfflineFallback` → tenta rede, salva no cache e usa `/offline` se falhar.
  - Assets estáticos (`style`, `script`, `image`, `font`): `cacheFirst` com preenchimento de cache sob demanda.
  - APIs: não são cacheadas (`/api/` é ignorado).
- Atualização:
  - `install` executa `skipWaiting`.
  - `activate` remove caches antigos e faz `clients.claim`.
  - Quando um novo SW instala, ele recebe `SKIP_WAITING` via postMessage; `controllerchange` força reload da página.
  - `registration.update()` roda a cada 1h no cliente para buscar nova versão.

## Página offline (`/offline`)
- Usa cores/tema e logo da congregação.
- Mostra mensagem de falta de conexão e botão “Tentar novamente” (reload).

## Registro e botão de instalação
- No `main.blade.php`:
  - `<link rel="manifest" href="{{ route('pwa.manifest') }}">` e `<meta name="theme-color">`.
  - Registro do service worker em `window.load`; força atualização e recarrega quando houver novo SW.
  - Lógica `beforeinstallprompt` guarda o evento e exibe o link “Baixar App” no dropdown; o clique dispara o prompt ou abre o manifesto como fallback.

## Como atualizar o SW
- Bump em `APP_VERSION` dentro de `public/service-worker.js` para invalidação rápida.
- Deploy da nova versão -> clientes buscarão nova build; `registration.update()` e `skipWaiting` garantem troca rápida.

## Dicas de teste rápido
1. Abra a aplicação no navegador e verifique DevTools > Application > Manifest (deve carregar o manifesto dinâmico).
2. Em Application > Service Workers, confirme o SW ativo; clique “Update” para forçar e veja a página recarregar.
3. Ative “Offline” no DevTools e recarregue: deve exibir a página `/offline`.
4. Teste o botão “Baixar App” no dropdown; em browsers suportados abrirá o prompt de instalação.
