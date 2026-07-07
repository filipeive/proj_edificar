# 00 — Visão Geral do Sistema

> **Projeto:** Portal Life Church (proj_edificar)
> **Versão atual:** 1.x
> **Data da auditoria:** 2026-07-08
> **Auditor:** Antigravity (Product Designer / UX Researcher / Frontend Architect)

---

## 1. Descrição do Produto

O **Portal Life Church** é um sistema de gestão eclesiástica completo, abrangendo:

- Gestão de membros e células
- Relatórios de cultos e estudos bíblicos
- Contribuições financeiras (dízimos/ofertas) com workflow de validação
- Gestão de zonas, supervisões e células
- Relatórios trimestrais
- Dashboard financeiro (Edificar)
- Gestão de eventos e casamentos
- Escola ministerial (cursos, turmas, inscrições)
- Inventário eclesiástico
- Gestão de visitantes
- Notificações internas
- PWA (Progressive Web App)

## 2. Stack Tecnológica

| Camada | Tecnologia | Versão |
|--------|-----------|--------|
| Backend | Laravel (PHP) | 10.x+ |
| Template Engine | Blade | — |
| CSS Framework | **TailwindCSS** (CDN + Vite) | 3.1+ |
| JS Framework | Alpine.js | 3.4+ |
| Build Tool | Vite | 5.x |
| Ícones | Bootstrap Icons | 1.11 |
| Gráficos | Chart.js | (CDN) |
| Notificações | SweetAlert2 | 11.x |
| Dropdowns pesquisáveis | Tom Select | 2.3.1 |
| Bundler CSS | PostCSS + Autoprefixer | — |
| PWA | Service Worker manual | — |

### ⚠️ Inconsistência Crítica Identificada

O projeto declara **Bootstrap 5** como tecnologia principal nos requisitos, mas **na prática utiliza TailwindCSS** como framework de CSS:
- `tailwind.config.js` presente na raiz
- `@tailwind base/components/utilities` em `resources/css/app.css`
- `<script src="https://cdn.tailwindcss.com">` carregado no layout principal (CDN runtime)
- Todas as classes no HTML são Tailwind utility classes
- **NÃO há Bootstrap CSS/JS importado**
- Ícones são **Bootstrap Icons** (apenas a fonte de ícones, não o framework Bootstrap)

**Conclusão:** O framework CSS real é TailwindCSS, não Bootstrap 5. A documentação deve ser atualizada para refletir isso.

## 3. Arquitetura de Views (Blade)

```
resources/views/
├── layouts/
│   ├── app.blade.php          # Layout principal (1711 linhas!)
│   ├── sidebar.blade.php      # Sidebar com navegação (481 linhas)
│   ├── admin.blade.php        # Layout admin
│   ├── auth.blade.php         # Layout de autenticação
│   ├── guest.blade.php        # Layout para visitantes
│   ├── header.blade.php       # Header parcial
│   └── navigation.blade.php   # Navegação legacy
├── components/                # 13 Blade Components
├── dashboard/                 # 7 dashboards por role
├── admin/                     # 13 módulos administrativos
├── services/                  # 9 views de cultos
├── contributions/             # 4 views de contribuições
├── cell_meetings/             # 5 views de encontros
├── members/                   # 4 views de membros
├── visitors/                  # 4 views de visitantes
├── events/                    # 5 views de eventos
├── reports/                   # 6+ views de relatórios
├── courses/                   # Views de cursos
├── auth/                      # 6 views de autenticação
└── ... (outros módulos)
```

## 4. Roles do Sistema

| Role | Dashboard | Descrição |
|------|-----------|-----------|
| `super_admin` / `admin` | `admin.blade.php` | Acesso total |
| `pastor_senior` / `pastor` | `pastor.blade.php` | Visão pastoral |
| `pastor_zona` | `pastor.blade.php` | Pastor de zona |
| `supervisor` | `supervisor.blade.php` | Supervisor de células |
| `lider_celula` | `lider.blade.php` | Líder de célula |
| `membro` | `membro.blade.php` | Membro regular |
| `secretaria` | `secretaria.blade.php` | Secretaria |
| `administracao` | `administracao.blade.php` | Administração |
| `comissao_obra` | — | Comissão de obra/edificar |
| `responsavel_pacote` | — | Gestor de pacotes |
| `tesouraria` | — | Tesouraria |
| `timoteo` | — | Discípulo (Timóteo) |

## 5. Fluxos Principais

1. **Autenticação** → Login → Dashboard (redirecionado por role)
2. **Contribuições** → Criar → Pendente → Verificar/Rejeitar → Confirmada
3. **Cultos** → Registar → Presenças por zona → Dízimos/Ofertas → Relatório PDF
4. **Células** → Zonas → Supervisões → Células → Membros → Encontros
5. **Relatórios** → Célula → Supervisão → Zona → Global
6. **Escola Ministerial** → Cursos → Turmas → Inscrições → Presença

## 6. Objetivo da Evolução

Transformar o sistema em um **produto SaaS moderno, profissional, leve, consistente, acessível, rápido e escalável**, com prioridade em:

1. ✅ Mobile First
2. ✅ Excelente UX
3. ✅ Performance
4. ✅ Simplicidade
5. ✅ Consistência
6. ✅ Design profissional
7. ✅ Manutenibilidade
