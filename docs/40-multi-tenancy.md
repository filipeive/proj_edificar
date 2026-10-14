# Multi-Tenancy por Congregação

## Objetivo e isolamento

O sistema usa uma base de dados partilhada e uma coluna obrigatória `congregation_id` nas tabelas operacionais. Os dados existentes são atribuídos à congregação primária **Life Church** pela migration `2026_10_14_090000_create_congregations_and_scope_business_tables`. A migration `2026_10_14_100000_scope_remaining_unique_constraints` converte as últimas unicidades globais de pacotes e tipos de evento em constraints compostas por tenant.

Cada congregação tem identidade, utilizadores, estrutura, serviços, finanças, inscrições, relatórios, eventos, configurações e inventário isolados. `cache`, `sessions`, `jobs`, `failed_jobs`, `job_batches`, `password_reset_tokens`, `personal_access_tokens` e `notifications` são infraestrutura partilhada: autenticação e notificações continuam ligadas ao utilizador; não consultar estas tabelas sem uma chave de utilizador ou de tenant.

## Como é resolvida a congregação

1. Domínio completo configurado em `congregations.domain`.
2. Subdomínio configurado em `congregations.subdomain`.
3. Campo `congregation` no pedido de login e sessão, útil em localhost/testes.
4. Tenant do utilizador autenticado, quando não há domínio explícito.
5. Congregação marcada `is_primary` como fallback, atualmente Life Church.

Um domínio/subdomínio explícito que não corresponde ao tenant da conta autenticada devolve `403`. Em produção, preferir domínios/subdomínios dedicados com HTTPS; não aceitar `X-Congregation-ID` do cliente como mecanismo de autorização.

## Centro Nacional do Super Admin

O centro nacional está em `/national/dashboard` (`national.dashboard`) e é reservado ao papel exatamente igual a `super_admin`, pelo middleware `super.admin`. Não usar o middleware `role:super_admin`: o helper legado `hasRole()` concede permissões implícitas também a `admin` e `pastor_senior`.

O painel lê agregados nacionais diretamente por `congregation_id`, dentro do controller protegido, e não troca o contexto do pastor para abrir os dados de outras igrejas. CRUD administrativo: `/national/congregations`; relatórios: `/national/reports` com exportação Excel e PDF. Administradores das congregações continuam no dashboard local.

O superadmin pode criar congregação e primeiro administrador em transação, editar nome/slug/domínio e ativar/desativar. A congregação primária não pode ser desativada nem substituída pela edição. O relatório nacional contém contagens e totais, não expõe listagens de membros/detalhes financeiros de outra congregação.

## Criar uma congregação

Depois das migrations, criar tenant e primeiro administrador pelo comando interativo:

```sh
php artisan congregation:create "Igreja Exemplo" igreja-exemplo \
  --subdomain=igreja-exemplo
```

O comando solicita email, nome e palavra-passe do administrador. Configure DNS e o virtual host/proxy para apontar o subdomínio à aplicação. Para domínio próprio:

```sh
php artisan congregation:create "Comunidade Exemplo" comunidade-exemplo \
  --domain=gestao.comunidade.org
```

Os endereços devem terminar em HTTPS antes de credenciais reais serem usadas. Os emails de utilizador são atualmente únicos globalmente; a mesma pessoa não pode ter duas contas com o mesmo email em tenants diferentes.

## Regras para código novo

- Todos os modelos com dados da congregação devem estender `App\\Models\\CongregationModel`. `User` estende `Authenticatable` e usa o trait `BelongsToCongregation` diretamente.
- Não adicionar scopes manuais por `congregation_id` em cada controller. O scope global filtra consultas Eloquent; novos modelos devem usar a base e a migration deve adicionar FK não nula para `congregations`.
- Relações e IDs recebidos por request têm de ser validados. `BelongsToCongregation` valida automaticamente FKs diretas para modelos tenant-scoped ao guardar. Não usar `withoutGlobalScope(s)` em fluxos web comuns.
- Para pivots usados por `attach`/`sync`, criar um Pivot model com `BelongsToCongregation`, declarar `using(...)` e incluir `congregation_id` no pivot. Caso contrário, gravações do pivot não herdam automaticamente o tenant.
- Settings e qualquer outro cache de dados têm de incluir `congregation_id` na cache key.
- Os dados partilhados com views são guardados nos atributos do objeto `Request`, não numa variável estática PHP; não guardar utilizador/contagens de tenant em caches globais de processo.
- Jobs, comandos e tarefas agendadas não têm tenant HTTP implicitamente. Iterar explicitamente por congregação e executar trabalho com `app(App\\Services\\CongregationContext::class)->run($congregation, fn () => ...)`; o contexto anterior é restaurado mesmo quando ocorre uma exceção. Os comandos `commitments:check-expiring` e `contributions:check-pending` já iteram todos os tenants ativos.
- Importações em lote devem receber o tenant de destino. Exemplo: `php artisan reports:import-bulk relatorios.xlsx --congregation=life-church`; sem opção usa apenas a congregação primária.
- Exports, PDFs, APIs, dashboards e pesquisa devem usar modelos com scope; consultas `DB::table` precisam de filtro explícito por utilizador/congregação.
- Seeds de catálogo executados para novos tenants devem definir o contexto antes de criar registos. Não copiar IDs fixos de Life Church para outra congregação.
- Ao introduzir uma nova tabela de negócio, incluir a tabela e respetivos pivots nesta migration ou criar migration incremental equivalente, atribuir/backfill dados, adicionar índice/FK e teste de isolamento antes do deploy.

## Modulação Funcional: Projeto Edificar vs Gestão Eclesiástica

Nem todas as congregações gerem campanhas de construção física ou pacotes de compromisso de obras. A migration `2026_10_14_120000_add_has_edificar_project_to_congregations_table` adicionou a flag booleana `has_edificar_project`:

- **Congregação Principal (Life Church)**: `has_edificar_project = true` (permanente). Tem acesso ao painel de obras do Projeto Edificar, pacotes de compromisso (`/admin/packages`), compromissos de membros (`/commitments`) e atribuição de papéis de comissão de obra.
- **Outras Congregações**: Por defeito `has_edificar_project = false`. Operam estritamente no âmbito **eclesiástico**: gestão de membros, cultos, células, supervisões, zonas, dízimos, ofertas de culto e finanças correntes da igreja local.
- **Proteção de Rotas**: O middleware `EnsureEdificarModule` (`module.edificar`) bloqueia o acesso a rotas de pacotes, compromissos e painel de engenharia com `404 Not Found` caso o tenant não possua o módulo ativo.
- **Formulários e Menus**: O menu lateral oculta automaticamente itens de obras e pacotes para utilizadores de congregações sem o módulo. No registo de contribuições, a seleção de pacotes é omitida e gravada como `package_id = null`.

## Finanças Eclesiásticas Nacionais vs Obras

Para manter a integridade dos relatórios consolidados nacionais:
1. **Entradas da Igreja Nacional**: Compreende estritamente as contribuições gerais da igreja (`contributions` com `whereNull('package_id')` e status `verificada`) somadas às receitas recolhidas nos cultos (dízimos em dinheiro/envelope, ofertas de culto de tipos eclesiásticos, ofertas especiais e ofertas individuais).
2. **Exclusão de Pacotes de Obras**: Contribuições vinculadas a pacotes de construção do Projeto Edificar (`package_id IS NOT NULL`) permanecem estritamente no painel e relatórios próprios da congregação detentora da obra, não inflando os totais eclesiásticos do país.
3. **Despesas e Saldo Líquido**: As saídas operacionais eclesiásticas (`expenses` com `scope = 'eclesiastico'`) são consolidadas e subtraídas das entradas, fornecendo o **Saldo Líquido Eclesiástico** nacional no dashboard e nas exportações (Excel/PDF).

## Gestão de Administradores Locais (Tenant Admins)

No Gabinete do Super Admin (`/national/congregations/{congregation}/edit`):
- O Super Admin pode visualizar todos os administradores e pastores seniores da congregação.
- Pode registar novos administradores locais via POST `national.congregations.admins.store`.
- Pode redefinir diretamente a palavra-passe de qualquer administrador local via POST `national.congregations.admins.reset-password`.
- Ambas as ações são executadas sob `CongregationContext::run($congregation, ...)`, garantindo que a mutação ocorre no contexto correto e respeita as verificações de integridade do trait `BelongsToCongregation`.

## Mapeamento Geográfico Nacional

- Cada congregação pode ter as coordenadas da sua sede administrativa (`latitude` e `longitude`).
- O formulário de edição de congregações inclui o seletor pontual Leaflet ("Localização da congregação").
- O Painel Nacional exibe o mapa interativo Leaflet de Moçambique com marcadores diferenciados (sede principal vs congregações ativas/inativas) e popups interativos com dados demográficos e link direto de edição.

## Testes obrigatórios

Cobrir pelo menos: listagens que não exibem dados de outro tenant; leitura/edição direta por ID de outro tenant; relações que tentem ligar registos de tenants diferentes; criação automática de `congregation_id`; pivot `sync`; exports/API; cache; tarefas em fila; bloqueio de rotas do Projeto Edificar quando desativado; e segregação financeira nos consolidados nacionais. `tests/Feature/CongregationIsolationTest.php` e `tests/Feature/NationalDashboardTest.php` cobrem integralmente estes cenários.

Antes do deploy, executar `php artisan migrate --force`, `npm run build` e `php artisan test`. Fazer backup validado da base antes da migration de backfill. Não reverter a migration base depois de criar tenants adicionais; o método `down` recusa essa operação para evitar eliminar dados de tenants.

