# Edificar Android

Primeiro núcleo da aplicação Android do Edificar.

## Decisão arquitetural

O backend Laravel continua a ser a fonte de verdade. A app Android não replica a base de dados nem a lógica de negócio do servidor.

- Laravel 12 + Sanctum: API e regras de negócio.
- Vue 3 + Vite: interface mobile empacotada.
- Capacitor: runtime Android e acesso a APIs nativas.
- Futuro: SQLite local + fila de sincronização para operações offline.
- Futuro: notificações push e armazenamento seguro do token.

## Desenvolvimento

Na pasta `mobile/`:

```bash
npm install
npm run build
npx cap init Edificar com.lifechurch.edificar --web-dir=dist
npx cap add android
npx cap sync android
npx cap open android
```

Para desenvolvimento contra o backend local, use:

```bash
VITE_API_BASE_URL=http://SEU_HOST:8000/api/v1 npm run dev
```

> O armazenamento persistente do token ainda não é considerado pronto para produção. A próxima etapa deve implementar armazenamento seguro nativo antes de distribuir a app.

## Fases

1. Fundação Android + autenticação.
2. Dashboard por papel.
3. Células, membros e presenças.
4. Offline-first e sincronização.
5. Notificações push.
6. Eventos, contribuições, relatórios e restantes módulos.
7. Testes de integração Android/API e publicação.
