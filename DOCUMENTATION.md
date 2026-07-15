# Documentação Técnica - Sistema de Gestão Eclesiástica

## 1. Visão Geral
O sistema foi desenvolvido para integrar a gestão administrativa, ministerial e financeira da igreja. Ele utiliza o framework Laravel 10+ e Tailwind CSS para a interface.

## 2. Arquitetura de Dados
### Modelos Principais e Relacionamentos
- **User**: Gerencia autenticação e papéis (Admin, Pastor, Supervisor, Líder, Tesouraria).
- **Zone / Supervision / Cell**: Estrutura hierárquica da igreja.
- **OfferingType**: Define categorias de entradas financeiras.
- **Service**: Registra cultos e suas respectivas ofertas (via `ServiceOffering`).
- **CellMeeting**: Registra reuniões semanais das células.
- **QuarterlyReport**: Consolida dados trimestrais de crescimento e saúde ministerial.
- **Event**: Registra eventos especiais e cerimônias.

## 3. Sistema de Permissões (Policies)
A autorização é baseada em `Policies` do Laravel:
- `ServicePolicy`: Controla quem pode registrar e visualizar cultos.
- `CellMeetingPolicy`: Garante que líderes vejam apenas suas células e supervisores vejam sua área.
- `QuarterlyReportPolicy`: Restringe a edição após a submissão e controla a visibilidade por zona.
- `EventPolicy`: Gerencia o acesso aos eventos.

## 4. Fluxos Financeiros
O sistema separa dízimos de membros (registrados via `Contribution`) de ofertas coletadas em cultos (registradas via `ServiceOffering`). O `FinancialDashboardController` consolida ambos os fluxos para uma visão unificada por tipo de oferta.

---

# Manual do Usuário

## Gestão de Cultos
1. Vá para **Gestão Eclesiástica > Cultos**.
2. Clique em **Novo Culto**.
3. Preencha a data, pregador, tema e contagem de participantes.
4. No painel lateral, insira os valores coletados para cada tipo de oferta.
5. Salve para gerar o registro e atualizar o painel financeiro.

## Encontros de Célula
1. Vá para **Gestão Eclesiástica > Encontros de Célula**.
2. Registre a reunião semanal informando a célula, tema do estudo e frequência.
3. Use o campo de observações para pedidos de oração ou relatos de decisões.

## Relatórios Trimestrais
1. Supervisores devem acessar **Relatórios > Trimestrais**.
2. Clique em **Novo Relatório**.
3. Insira as estatísticas da zona (membros, batismos, multiplicações).
4. Avalie os indicadores qualitativos de 1 a 10.
5. Submeta o relatório para que o Pastor de Zona possa visualizar o consolidado.

---

# Guia de Deployment

## Requisitos do Servidor
- PHP 8.1+
- MySQL 8.0+
- Node.js 20+ (para compilação do Vite)
- Composer

## Passos para Instalação
1. Clone o repositório.
2. Execute `composer install --no-dev --optimize-autoloader`.
3. Configure o arquivo `.env` com as credenciais do banco de dados.
4. Execute `php artisan migrate --force` para criar as tabelas.
5. Execute `php artisan db:seed --class=RoleSeeder` para criar os usuários iniciais.
6. Execute `php artisan db:seed --class=OfferingTypeSeeder` e `EventTypeSeeder`.
7. Compile os assets: `npm install && npm run build`.
8. Configure o servidor web (Nginx/Apache) para apontar para a pasta `public`.

## Atualização do Node.js (Vite)
O sistema foi configurado para usar o **Vite 5**, que é compatível com o **Node.js v18.19.1**. Se desejar utilizar versões mais recentes do Vite (v6+), será necessário atualizar o Node.js para v20+:
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs
```

---

## 5. API V1 & Arquitetura Híbrida
A partir da versão 1.1.0, o sistema adota uma arquitetura híbrida (Web + API REST) utilizando o **Laravel Sanctum**. As regras de negócio complexas de reatribuição de membros (`ReassignMemberAction`) e de inscrições (`EnrollMemberAction`) foram extraídas para classes independentes de serviço (Actions) compartilhadas entre a aplicação Web e os endpoints da API.

A documentação OpenAPI detalhada dos endpoints, payloads de requisição e estruturas de resposta JSON encontra-se no ficheiro [docs/36-api-specification.md](file:///home/fdev-ms/Filipe/proj_edificar/docs/36-api-specification.md).
