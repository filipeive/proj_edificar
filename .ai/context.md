# Contexto do Projeto — Memória Operacional

> **Projeto:** Portal Life Church (proj_edificar)
> **Sessão atual:** 2026-07-08
> **Branch:** `feature/ui-redesign-saas`

---

## Estado Atual

- **Fase:** Fase 4 — Implementação em progresso
- **Ciclo:** 1 (Quick Wins) — ✅ COMPLETO
- **Decisões tomadas:** 3 (DD-001, DD-002, DD-003)
- **Itens no backlog:** 22 (5 concluídos, 17 pendentes)

## Ciclo 1 — Status

| ID | Título | Status |
|----|--------|--------|
| QW-01 | Remover TailwindCSS CDN Runtime | ✔️ Implementado |
| QW-02 | Eliminar Flash Messages Duplicadas | ✔️ Implementado |
| QW-03 | Corrigir CSS Sidebar Conflitante | ✔️ Implementado |
| QW-04 | Remover x-cloak Duplicados | ✔️ Implementado |
| QW-05 | Mostrar Métricas no Mobile | ✔️ Implementado |

## Restrições Confirmadas

1. Framework CSS: **TailwindCSS** (não Bootstrap)
2. Framework JS: **Alpine.js** (sem frameworks pesados)
3. Branch dedicada: `feature/ui-redesign-saas`

## Ficheiros Alterados no Ciclo 1

| Ficheiro | Alterações |
|---------|-----------|
| `resources/views/layouts/app.blade.php` | QW-01, QW-02, QW-03, QW-04 |
| `resources/views/dashboard/admin.blade.php` | QW-05 |

## Próximo: Ciclo 2 (Performance & Organização)

| ID | Título | Estimativa |
|----|--------|-----------|
| MI-01 | Extrair CSS inline para ficheiro | 2-3h |
| MI-02 | Extrair JS inline para módulos | 3-4h |
| MI-03 | Internalizar dependências CDN | 2-3h |

- [ ] Aguardar aprovação do Ciclo 2
- [ ] Implementar apenas itens aprovados
- [ ] Documentar cada alteração imediatamente
