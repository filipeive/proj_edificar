# ARCHITECTURE DECISIONS (ADR) — Life Church Management System

> **Data:** 2026-07-10 | **Responsável:** Chief Digital Transformation Architect

---

## 1. [ADR-001] Escolha do Stack Frontend (Tailwind CSS + Alpine.js)

- **Contexto:** Havia requisitos sugerindo o uso de Bootstrap 5, porém a base de código encontrava-se estruturada sob classes utilitárias do Tailwind CSS e diretivas inline do Alpine.js.
- **Decisão:** Manter o uso do **Tailwind CSS (3.x)** e **Alpine.js (3.x)**, rejeitando a migração para Bootstrap 5.
- **Justificação:**
  - Evita reescrita completa das centenas de views compiladas.
  - Alpine.js é extremamente leve (~15KB) e integra-se perfeitamente com o Blade do Laravel para interações simples sem a necessidade de um build complexo de Single Page Application (SPA).
  - Tailwind CSS oferece melhor suporte a design systems customizados baseados em CSS variables.

---

## 2. [ADR-002] Dupla Camada de Autorização (Middleware + Policies)

- **Contexto:** Necessidade de controlar o acesso a rotas de forma geral e, simultaneamente, aplicar regras de negócio granulares sobre quem pode ver ou editar registos específicos.
- **Decisão:** Implementar autorização em duas camadas:
  1. **Middleware `CheckRole`:** Filtra o acesso a grupos de rotas com base no papel do utilizador.
  2. **Laravel Policies:** Valida a autoria e a hierarquia eclesiástica ao nível do registo (ex: se o líder pertence à mesma célula do membro).
- **Justificação:**
  - Desempenho: O middleware bloqueia acessos indevidos antes de carregar dados da base de dados.
  - Segurança: As policies garantem que um supervisor não consiga alterar dados de outra supervisão através da alteração direta de IDs na URL.

---

## 3. [ADR-003] Separação de Fluxos Financeiros (Eclesiástico vs Campanha)

- **Contexto:** A igreja possui dízimos/ofertas de cultos normais e contribuições específicas do Projeto Edificar (Campanha de Obras).
- **Decisão:**
  - **Tabela `services`:** Regista dízimos e ofertas arrecadadas nos cultos de adoração física (financeiro geral).
  - **Tabela `contributions`:** Regista dízimos e contribuições financeiras individuais de membros. Se `package_id` for nulo, refere-se ao dízimo eclesiástico. Se preenchido, refere-se ao Projeto Edificar.
- **Justificação:**
  - Garante o isolamento estrito dos dados da campanha de construção, facilitando auditorias financeiras e prestação de contas separadas.

---

## 4. [ADR-004] Ciclo do Mês Financeiro (20 a 5) vs Mês de Calendário

- **Contexto:** Os dízimos dos membros e as contribuições de pacotes seguem o ciclo de vencimento do país, do dia 20 do mês ao dia 5 do mês seguinte. No entanto, os cultos e eventos de calendário seguem o mês normal.
- **Decisão:** 
  - Usar o intervalo do **20º dia do mês ao 5º dia do mês seguinte** para todos os cálculos de dízimos de membros e progresso de pacotes (`getTotalContributedThisMonth`).
  - Usar o **mês de calendário (1 a 30/31)** nos dashboards administrativos gerais de cultos e presenças.
- **Justificação:**
  - Alinha o sistema com os hábitos reais de contribuição da membresia local, evitando relatórios com progresso "zero" no início do mês civil.

---

## 5. [ADR-005] Abstração de Envio de SMS (Strategy Pattern)

- **Contexto:** A aplicação necessita de enviar notificações de visitantes para os líderes. O provedor de SMS pode mudar com base no custo ou disponibilidade técnica em Moçambique.
- **Decisão:** Criar uma interface comum [SmsProviderInterface](file:///home/fdev-ms/Filipe/proj_edificar/app/Services/Sms/SmsProviderInterface.php) e implementar múltiplos drivers (`HttpsmsProvider`, `MoceanSmsProvider`, `LogSmsProvider`). O serviço [SmsService](file:///home/fdev-ms/Filipe/proj_edificar/app/Services/Sms/SmsService.php) serve de fachada unificada.
- **Justificação:**
  - Permite trocar o provedor de SMS (ex: de httpSMS para Mocean ou Log em desenvolvimento) alterando apenas uma variável no ficheiro `.env`, sem alterar nenhuma linha de código nos models ou controllers.
