# Especificação Técnica da API V1 - Projeto Edificar

Esta especificação define os endpoints, payloads, cabeçalhos e fluxos de dados para a API V1, com vista a suportar a integração com a aplicação móvel (Flutter) ou decoupling completo do frontend.

---

## 1. Informações Gerais e Convenções

### 1.1. URL Base
```
https://edificar.membros.co.mz/api/v1
```

### 1.2. Cabeçalhos Globais
Todos os endpoints protegidos requerem os seguintes cabeçalhos HTTP:

| Cabeçalho | Valor | Descrição |
|---|---|---|
| `Accept` | `application/json` | Requerido para garantir que o Laravel retorne respostas JSON. |
| `Content-Type` | `application/json` | Requerido para requisições com payload. |
| `Authorization` | `Bearer <TOKEN>` | Token de acesso pessoal gerado pelo Laravel Sanctum. |

### 1.3. Padrão Global de Resposta JSON
Todas as respostas seguem uma estrutura padrão definida em `BaseApiController`:

#### Resposta de Sucesso (HTTP 200/201)
```json
{
  "success": true,
  "message": "Mensagem informativa.",
  "data": { ... } ou [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 12
  },
  "errors": null
}
```

#### Resposta de Erro (HTTP 4xx/5xx)
```json
{
  "success": false,
  "message": "Mensagem descritiva do erro.",
  "errors": {
    "campo": [
      "Mensagem de validação do campo."
    ]
  }
}
```

---

## 2. Autenticação (Auth)

### 2.1. Login
* **Rota:** `POST /login`
* **Autenticação:** Pública (Sem Token)
* **Payload da Requisição:**
```json
{
  "email": "pastor@edificar.org",
  "password": "senha_segura",
  "device_name": "iPhone 15 Pro"
}
```
* **Resposta de Sucesso (HTTP 200):**
```json
{
  "success": true,
  "message": "Autenticação realizada com sucesso.",
  "data": {
    "token": "4|vX8...L1",
    "user": {
      "id": 1,
      "name": "Pastor Principal",
      "email": "pastor@edificar.org",
      "phone": "+258840000000",
      "role": "pastor_zona",
      "cell_id": null,
      "is_active": true
    }
  },
  "meta": [],
  "errors": null
}
```

### 2.2. Perfil
* **Rota:** `GET /profile`
* **Autenticação:** Requerida (`Bearer Token`)

### 2.3. Terminar Sessão (Logout)
* **Rota:** `POST /logout`
* **Autenticação:** Requerida (`Bearer Token`)

---

## 3. Painel de Controle (Dashboard)

* **Rota:** `GET /dashboard`
* **Autenticação:** Requerida (`Bearer Token`)
* **Descrição:** Retorna os indicadores e dados rápidos do dashboard adaptados ao perfil do utilizador autenticado (consome o `DashboardService`).

---

## 4. Membros

### 4.1. Listagem de Membros
* **Rota:** `GET /members`
* **Autenticação:** Requerida (`Bearer Token`)
* **Parâmetros de Consulta:**
  * `search` (opcional): Filtro por nome, e-mail ou telefone.
  * `role` (opcional): Filtro por perfil (`membro`, `lider_celula`, etc.).
  * `status` (opcional): Filtro por atividade (`active` ou `inactive`).
  * `cell_id` (opcional): Filtro por célula.

### 4.2. CRUD completo
* `POST /members` (Criar)
* `GET /members/{member}` (Detalhes)
* `PUT /members/{member}` (Atualizar)
* `DELETE /members/{member}` (Remover)

---

## 5. Células

### 5.1. Listar Células
* **Rota:** `GET /cells`
* **Autenticação:** Requerida (`Bearer Token`)

### 5.2. Transferir Membro de Célula
* **Rota:** `POST /cells/transfer-member`
* **Autenticação:** Requerida (`Bearer Token`)
* **Payload da Requisição:**
```json
{
  "member_id": 5,
  "cell_id": 3
}
```

---

## 6. Ministérios (Inscrições Ministeriais)

* **Rota:** `GET /ministries`
* **Autenticação:** Requerida (`Bearer Token`)
* **CRUD completo:** `GET /ministries`, `POST /ministries`, `GET /ministries/{id}`, `PUT /ministries/{id}`, `DELETE /ministries/{id}`.

---

## 7. Eventos e Matrícula de Cursos

### 7.1. Listagem de Eventos
* **Rota:** `GET /events`
* **Autenticação:** Requerida (`Bearer Token`)

### 7.2. Matrícula em Curso
* **Rota:** `POST /events/{course}/enroll`
* **Autenticação:** Requerida (`Bearer Token`)

---

## 8. Controlo de Presenças (Attendance)

### 8.1. Registar Presenças em Lote
* **Rota:** `POST /attendance`
* **Autenticação:** Requerida (`Bearer Token`)
* **Payload da Requisição:**
```json
{
  "cell_id": 2,
  "records": [
    {
      "user_id": 5,
      "date": "2026-07-11",
      "type": "sabado",
      "status": true,
      "reason": ""
    }
  ]
}
```

---

## 9. Relatórios Financeiros (Reports)

* **Rota:** `GET /reports/contributions`
* **Autenticação:** Requerida (`Bearer Token`)

---

## 10. Notificações

* **Rota:** `GET /notifications`
* **Autenticação:** Requerida (`Bearer Token`)

---

## 11. Casamentos (Weddings)

* **Rotas CRUD:**
  * `GET /weddings` (Listagem)
  * `POST /weddings` (Criação)
  * `GET /weddings/{wedding}` (Detalhes)
  * `PUT /weddings/{wedding}` (Atualização)
  * `DELETE /weddings/{wedding}` (Remoção)
* **Status aceites:** `scheduled`, `completed`, `cancelled`.
* **Payload de criação:**
```json
{
  "groom_name": "António Silva",
  "bride_name": "Maria Santos",
  "date": "2026-09-10",
  "time": "14:00:00",
  "location": "Templo Central",
  "status": "scheduled"
}
```

---

## 12. Relatórios Trimestrais (Quarterly Reports)

* **Rotas CRUD:**
  * `GET /quarterly-reports` (Listagem)
  * `POST /quarterly-reports` (Criação)
  * `GET /quarterly-reports/{report}` (Detalhes)
  * `PUT /quarterly-reports/{report}` (Atualização)
  * `DELETE /quarterly-reports/{report}` (Remoção)
* **Status aceites:** `draft`, `submitted`.

---

## 13. Requisições e Despesas (Requisitions & Expenses)

### 13.1. Requisitions (Pedidos de Fundos)
* **Rotas CRUD:**
  * `GET /requisitions` (Listagem)
  * `POST /requisitions` (Criação)
  * `GET /requisitions/{requisition}` (Detalhes)
  * `PUT /requisitions/{requisition}` (Atualização)
  * `DELETE /requisitions/{requisition}` (Remoção)
* **Ações administrativas:**
  * `POST /requisitions/{requisition}/approve` (Aprovar)
  * `POST /requisitions/{requisition}/reject` (Rejeitar, com payload `{"rejection_reason": "Texto"}`)

### 13.2. Expenses (Lançamento de Despesas)
* **Rotas CRUD:**
  * `GET /expenses` (Listagem)
  * `POST /expenses` (Lançamento de despesa associada a uma requisição)

---

## 14. Dízimos de Membros (Contributions)

* **Rotas CRUD:**
  * `GET /contributions` (Listagem)
  * `POST /contributions` (Criação/Envio de comprovante de dízimo)
* **Ações administrativas de Verificação:**
  * `POST /contributions/{contribution}/verify` (Confirmar dízimo)
  * `POST /contributions/{contribution}/reject` (Rejeitar dízimo, com payload `{"rejection_reason": "Texto"}`)
  * `POST /contributions/{contribution}/cancel` (Cancelar dízimo verificado)

---

## 15. Cultos (Services)

* **Rotas CRUD:**
  * `GET /services` (Listagem)
  * `POST /services` (Criação/Registo de culto e frequência)
* **Payload de criação:**
```json
{
  "date": "2026-07-12",
  "service_type": "Domingo de Celebração",
  "preacher_name": "Pastor João",
  "theme": "A Graça que Transforma",
  "adults_members": 120,
  "adults_visitors": 15,
  "adults_salvations": 3,
  "children_members": 40,
  "children_visitors": 8,
  "children_salvations": 1
}
```

---

## 16. Pacotes de Compromisso (Packages)

* **Rotas CRUD:**
  * `GET /packages` (Listagem de pacotes activos/inativos)
  * `POST /packages` (Criação)

---

## 17. Inventário (Inventory Items)

* **Rotas CRUD:**
  * `GET /inventory-items` (Listagem)
  * `POST /inventory-items` (Cadastro de bens)
