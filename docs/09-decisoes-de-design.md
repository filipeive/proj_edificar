# 09 — Decisões de Design

> Registo de todas as decisões de design tomadas durante a evolução da interface.

---

## DD-001: Framework CSS Real é TailwindCSS

- **Data:** 2026-07-08
- **Contexto:** O briefing mencionava Bootstrap 5, mas o projeto utiliza TailwindCSS (via Vite + CDN).
- **Decisão:** Manter TailwindCSS como framework CSS. Não migrar para Bootstrap 5.
- **Justificação:**
  - O projeto já tem centenas de views com classes Tailwind
  - Migrar para Bootstrap seria uma reescrita total
  - TailwindCSS é mais adequado para design systems customizados
  - Alpine.js + TailwindCSS é uma combinação mais leve que Bootstrap + jQuery
- **Impacto:** Toda documentação e padrões devem referenciar TailwindCSS, não Bootstrap.

## DD-002: Manter Alpine.js

- **Data:** 2026-07-08
- **Contexto:** Consideração de usar framework mais pesado (Vue, React).
- **Decisão:** Manter Alpine.js como framework JS frontend.
- **Justificação:**
  - Já integrado em todas as views
  - Leve (~15KB vs ~100KB+ de Vue/React)
  - Complementa bem o stack Blade + Tailwind
  - Suficiente para as interações do sistema
- **Impacto:** Nenhuma dependência adicional de JS framework.

## DD-003: Sidebar Sempre Preto

- **Data:** 2026-07-08
- **Contexto:** O sidebar tem `bg-black` fixo, mesmo em modo escuro.
- **Decisão:** Manter sidebar escuro (preto) em ambos os temas por agora.
- **Justificação:**
  - Cria contraste profissional com o conteúdo
  - Padrão de SaaS moderno (Notion, Linear, Vercel)
  - Ajuda na orientação visual do utilizador
- **Impacto:** Sidebar não precisa de dark mode variants para background.

---

*Novas decisões serão adicionadas conforme aprovação de implementações.*
