# 00 — RITUAL DE INICIAÇÃO PARA AGENTES DE IA (ONBOARDING)

> **ATENÇÃO AGENTE:** Este é o documento número zero. Se és um Agente de Inteligência Artificial (LLM, Coding Assistant ou Copilot) a atuar neste repositório, **ESTE É O TEU PONTO DE PARTIDA MANDATÓRIO**. Deves ler e executar este ritual antes de propor ou editar qualquer linha de código.

---

## O Ritual de Inicialização (Passo a Passo)

Qualquer agente que assuma o papel de desenvolvedor ou arquiteto neste sistema deve seguir estritamente o seguinte protocolo de onboarding:

### Passo 1: Leitura do Índice Mestre
Deves ler imediatamente o ficheiro [docs/README.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/README.md) para compreender a organização de toda a biblioteca técnica do projeto.

### Passo 2: Alinhamento com a Arquitetura e Base de Dados
Antes de qualquer alteração, lê estes três documentos essenciais:
1. [20-project-overview.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/20-project-overview.md) — Para entender o que o sistema faz, as pilhas tecnológicas e perfis de utilizador.
2. [21-architecture.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/21-architecture.md) — Para compreender as camadas lógicas (MVC, Policies, Middleware, Services).
3. [22-database.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/22-database.md) — Para entender os relacionamentos e regras de integridade física.

### Passo 3: Análise de Estado Atual, Dívidas e Histórico
Deves tomar consciência das limitações e do progresso recente:
1. [32-project-status.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/32-project-status.md) — Para verificar o estado das funcionalidades e saber se há testes em falha (atenção ao bug de teste do SQLite vs MySQL).
2. [33-technical-debt.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/33-technical-debt.md) — Lista de "pecados" do código (anti-padrões, acoplamento de HTML em models, etc.). Não dupliques estes erros!
3. [10-changelog-ui.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/10-changelog-ui.md) e [15-backlog-ui.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/15-backlog-ui.md) — O que foi alterado recentemente e quais pendências visuais existem.
4. [35-roadmap.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/35-roadmap.md) — O plano de evolução para saberes para onde o projeto caminha.

---

## Regras de Conduta para o Agente

> [!IMPORTANT]
> ### Regra de Ouro: Sincronização Absoluta
> **A documentação deste projeto é tão importante quanto o código funcional.** Nunca trates a documentação como um detalhe secundário. 

Como agente, deves cumprir as seguintes leis de desenvolvimento:

1. **Investigar antes de Codificar:** Nunca inventes caminhos ou tabelas. Procura referências em [23-modules.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/23-modules.md) e [25-routes.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/25-routes.md).
2. **Respeitar os ADRs:** Segue as decisões históricas descritas em [34-architecture-decisions.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/34-architecture-decisions.md) (ex: usar Tailwind e Alpine, manter sidebar escuro, respeitar o ciclo financeiro do dia 20 a 5).
3. **Manter Tudo Atualizado:**
   - Se criares ou modificares um **Model**, deves atualizar [27-models.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/27-models.md) e a modelagem do diagrama ER no [22-database.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/22-database.md).
   - Se adicionares ou editares uma **Rota**, atualiza [25-routes.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/25-routes.md).
   - Se refatorares ou criares um **Controller**, atualiza [26-controllers.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/26-controllers.md).
   - Qualquer nova entrega deve ser documentada no [10-changelog-ui.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/10-changelog-ui.md) ou num novo ficheiro de changelog apropriado.
4. **Trabalhar em Branches Dedicadas:** Nunca realizes alterações diretas na branch `main`. Inicia sempre o trabalho criando uma nova branch descritiva (ex: `feature/redesign-premium`) a partir da branch principal estável.
5. **Testar e Compilar:** Executa sempre testes locais (com atenção às diferenças entre MySQL/SQLite nas migrações cruas) e compila os assets estáticos via Vite (`npm run build`) para validar a integridade do código e a ausência de erros de sintaxe ou transpilação.
6. **Sem Placeholder de Código:** Quando te for pedido para editar um ficheiro, não omitas código circundante inalterado. Preserva a integridade dos comentários, docstrings e estruturas originais.
7. **Autenticação de Segurança:** Ao mexer em formulários ou acessos, respeita estritamente o modelo de autorização dupla (Middleware e Policies) detalhado no [29-security.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/29-security.md).

---

## Declaração de Compromisso do Agente
Ao prosseguires para a tua tarefa, deves responder ao utilizador com uma confirmação curta declarando que:
1. Concluíste o Ritual de Iniciação.
2. Compreendeste o estado atual, a arquitetura e os pontos de dívida do sistema.
3. Comprometes-te a manter todas as documentações atualizadas em conformidade com as alterações de código efetuadas.
