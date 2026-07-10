# PERFORMANCE — Life Church Management System

> **Data:** 2026-07-10 | **Responsável:** Chief Digital Transformation Architect

---

## 1. Problemas de Queries N+1 Identificados

O sistema apresenta alguns gargalos clássicos de N+1 (múltiplas queries SQL executadas dentro de ciclos):

### 1.1 Dashboard do Administrador (`AdminDashboardController`)
No ciclo que lista as zonas e suas estatísticas, são executadas três queries por cada zona na base de dados:
1. Uma query de contagem de membros ativos por zona.
2. Uma query de contagem de supervisões por zona.
3. Uma query de contagem de células por zona.

**Impacto:** Para 10 zonas, são feitas 30 queries adicionais ao carregar o dashboard.  
**Solução Recomendada:** Utilizar `withCount` no carregamento inicial da coleção de zonas:
```php
$zones = Zone::withCount(['supervisions', 'cells'])
    ->withCount(['members' => function($q) { $q->where('is_active', true); }])
    ->get();
```

### 1.2 Listagem de Compromissos de Utilizadores (`UserCommitment`)
O model `UserCommitment` possui helpers para calcular a percentagem de progresso, total contribuído e total pendente. Estes helpers realizam queries diretas à tabela `contributions` (`Contribution::where(...)`):
- Se uma view listar 50 membros com os seus compromissos e percentagem de progresso, o Eloquent disparará 150 queries SQL adicionais.

---

## 2. Estratégia de Caching

### 2.1 Caching de Configurações (`Setting::get`)
O sistema utiliza de forma correta o cache para evitar leituras repetidas da tabela `settings` na base de dados:
- O valor é guardado em cache por 3600 segundos (1 hora).
- Quando a configuração é atualizada, o cache da chave específica é invalidado (`Cache::forget`).

> [!WARNING]
> ### Problema na limpeza de cache
> O método `Setting::clearCache()` executa `Cache::flush()`. Se o driver de cache for partilhado (como Redis), isto apagará sessões de utilizadores, filas e outros dados em cache na mesma máquina. Deveria ser limpo apenas o namespace das configurações.

### 2.2 Memoization por Request
No model `User` e no `AppServiceProvider`, são utilizadas variáveis de cache em memória estática (`static $cachedData` e propriedades memoizadas como `$this->memoizedZoneIds`):
- Evita que múltiplos componentes Blade no mesmo request façam a mesma query para obter dados globais ou permissões do utilizador logado.

---

## 3. Estrutura de Índices na Base de Dados

### Pontos Fortes (Tabelas Otimizadas)
- A tabela `contributions` está muito bem indexada: chaves estrangeiras (`user_id`, `cell_id`, `zone_id`), data da contribuição (`contribution_date`) e estado (`status`) possuem índices individuais.
- A tabela `visitors` possui índice composto nas colunas mais filtradas: `['zone_id', 'contact_status']`.

### Oportunidades de Melhoria
- A tabela `users` não possui índice na coluna `is_active` ou `role`. Visto que o sistema realiza filtragens frequentes por membros ativos e papéis para dashboards e relatórios, estes índices reduziriam o tempo de execução das queries.

---

## 4. Otimização de Assets (Frontend)

- **Vite compilation:** O sistema usa o Vite para minificar e concatenar ficheiros de Javascript e CSS.
- **PWA Service Worker:** O ficheiro `pwa.js` e o service worker auxiliam no cache de assets estáticos (fontes, ícones) diretamente no navegador do utilizador final, poupando largura de banda.

---

## 5. Recomendações e Plano de Otimização

| Ação | Prioridade | Impacto | Descrição |
|------|------------|---------|-----------|
| **Fila de Processamento (Queues)** | 🔴 Alta | Alto | Configurar `QUEUE_CONNECTION=database` ou Redis. Tarefas lentas como geração de relatórios trimestrais e envio de SMS em massa (atualmente síncronas) devem correr em background para evitar timeouts HTTP (504). |
| **Eager Loading nos Dashboards** | 🔴 Alta | Médio | Refatorar os dashboards para carregar as relações e contagens de forma agrupada (`with` e `withCount`). |
| **Redis como Cache Driver** | 🟡 Média | Médio | Mudar o driver de cache em produção de `file` para `redis` para acelerar o acesso às configurações e sessões. |
| **Compressão de Ficheiros** | 🟡 Média | Baixo | Adicionar compressão de imagens de comprovativos no servidor para evitar consumo excessivo de storage. |
