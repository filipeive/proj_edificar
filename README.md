# 🏛️ Life Church - Sistema de Gestão Eclesiástica

Sistema completo de gestão para igrejas celulares, desenvolvido em Laravel 11.

## 🌐 Demonstração

**Sistema em Produção**: [http://146.235.224.99/edificar/](http://146.235.224.99/edificar/)

## 📋 Requisitos do Sistema

- PHP >= 8.2
- Composer
- MySQL >= 5.7 ou MariaDB >= 10.3
- Node.js >= 18 (para assets)
- Extensões PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## 🚀 Instalação do Zero

### 1. Clonar o Repositório

```bash
git clone https://github.com/filipeive/proj_edificar.git
cd proj_edificar
```

### 2. Instalar Dependências

```bash
composer install
npm install && npm run build
```

### 3. Configurar Ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar Banco de Dados

Edite o arquivo `.env` e configure as credenciais do banco:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha
```

**Criar o banco de dados:**
```bash
mysql -u root -p
CREATE DATABASE nome_do_banco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 5. Executar Migrations e Seeds

```bash
php artisan migrate --seed
```

Isso irá:
- ✅ Criar todas as tabelas necessárias
- ✅ Criar configurações padrão do sistema
- ✅ Popular tipos de eventos, ofertas, etc.

### 6. Criar Storage Link

```bash
php artisan storage:link
```

### 7. Acessar o Setup Wizard

Inicie o servidor:
```bash
php artisan serve
```

Acesse: `http://localhost:8000/setup`

O wizard irá guiá-lo através de:
1. **Informações da Igreja** - Nome, contatos, endereço
2. **Primeiro Administrador** - Criar usuário admin
3. **Personalização Visual** - Cores do tema
4. **Finalização** - Sistema pronto para uso

### 8. Login

Após completar o setup, faça login com as credenciais criadas em:
`http://localhost:8000/login`

---

## 🔧 Configurações Adicionais

### Email (Opcional)

Para enviar notificações por email, configure no `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@suaigreja.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Timezone

Ajuste o fuso horário no `.env`:

```env
APP_TIMEZONE=Africa/Maputo
```

---

## 📁 Estrutura do Sistema

### Módulos Principais

- **Dashboard** - Painéis personalizados por papel (Admin, Pastor, Supervisor, Líder, Membro)
- **Células** - Gestão de células, supervisões e zonas
- **Membros** - Cadastro e acompanhamento de membros
- **Cultos** - Registro de cultos com dízimos, ofertas e participação
- **Eventos** - Calendário de eventos e cerimônias
- **Contribuições** - Gestão financeira (Projeto Edificar)
- **Relatórios** - Relatórios trimestrais e consolidados
- **Configurações** - Personalização do sistema

### Papéis de Usuário

1. **Admin** - Acesso total ao sistema
2. **Secretaria** - Gestão administrativa e eclesiástica
3. **Pastor de Zona** - Gestão de zona específica
4. **Supervisor** - Gestão de supervisão
5. **Líder de Célula** - Gestão de célula
6. **Membro** - Acesso limitado (dashboard pessoal)

---

## 🎨 Personalização

Após instalação, acesse **Configurações** (sidebar) para:

- Alterar nome e logo da igreja
- Personalizar cores do tema
- Configurar moeda e formatos regionais
- Gerenciar pacotes de compromisso

---

## 🔄 Atualização do Sistema

```bash
git pull origin main
composer install
npm install && npm run build
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🐛 Troubleshooting

### Erro de Permissões

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Limpar Cache

```bash
php artisan optimize:clear
```

### Recriar Banco de Dados

```bash
php artisan migrate:fresh --seed
```
⚠️ **ATENÇÃO**: Isso apagará todos os dados!

---

## 📞 Suporte

Para questões ou suporte, entre em contato através do repositório GitHub.

---

## 🙏 Créditos

**Desenvolvido por:**
- **Engenheiro Filipe dos Santos** - Desenvolvimento e Arquitetura do Sistema
- **Pastor Luis Sabonete** - Consultoria e Requisitos Eclesiásticos

Desenvolvido para **Life Church - Moçambique**

---

## 📄 Licença

Este projeto é proprietário e destinado ao uso da Life Church.

---

## 🙏 Créditos

Desenvolvido para Life Church - Moçambique
