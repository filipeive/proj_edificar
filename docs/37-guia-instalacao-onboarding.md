# 📖 Guia de Instalação e Onboarding para Novas Congregações / Campus

Este guia foi elaborado para orientar os administradores e equipas técnicas de **novas congregações, campus ou igrejas parceiras** na instalação e configuração inicial do **Portal Life Church (Sistema Edificar)**.

---

## 📋 1. Pré-Requisitos do Sistema

Antes de iniciar a instalação, certifique-se de que o computador ou servidor cumpre os seguintes requisitos:

* **Sistema Operativo:** Linux (Ubuntu 20.04/22.04/24.04 recomendado), Windows com Laragon/XAMPP, ou ambiente Docker.
* **PHP:** Versão 8.2 ou superior com as seguintes extensões ativas:
  - `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`
* **Base de Dados:** MySQL 8.0+ ou MariaDB 10.5+.
* **Servidor Web:** Nginx ou Apache.
* **Composer:** Para gestão de pacotes PHP (opcional se a pasta `vendor` já estiver incluída).

---

## 🚀 2. Instalação em 1 Clique (Via Script `install.sh`)

Se estiver a instalar num servidor Linux ou terminal Bash:

1. **Navegue para a pasta raiz do projeto:**
   ```bash
   cd /caminho/para/proj_edificar
   ```

2. **Configure o acesso à Base de Dados no ficheiro `.env`:**
   Se ainda não tiver o ficheiro `.env`, copie o exemplo:
   ```bash
   cp .env.example .env
   ```
   Edite o ficheiro `.env` e coloque as credenciais da sua base de dados MySQL:
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=edificar_db
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```

3. **Execute o script automatizado de instalação:**
   ```bash
   ./install.sh
   ```
   *O script irá verificar os requisitos do PHP, gerar a chave de encriptação, criar as tabelas na base de dados, efetuar a ligação dos arquivos de media (`storage:link`) e otimizar as caches do sistema.*

---

## 🎨 3. Assistente de Configuração Visual no Browser (Setup Wizard)

Após a execução do script (ou na primeira visita ao sistema), abra o seu navegador web (Google Chrome, Firefox ou Edge) e aceda ao endereço:

👉 **`http://seu-dominio-ou-ip/setup`** *(ou `http://146.235.224.99/edificar/setup`)*

O assistente guiado irá conduzi-lo através de **4 passos interativos**:

```
[ Passos do Onboarding ]
Step 1: Congregação ➔ Step 2: Pastor/Admin ➔ Step 3: Estrutura & Marca ➔ Step 4: Conclusão
```

### 🔹 Passo 1: Identificação da Congregação / Campus
- **Nome da Congregação / Igreja:** Nome da sua congregação local (ex.: *Life Church - Congregação de Quelimane*).
- **E-mail Institucional:** E-mail oficial da congregação para notificações (ex.: *quelimane@lifechurch.org*).
- **Telefone de Contacto:** Telefone da secretaria da igreja.
- **Cidade / Distrito & Província:** Localização física da congregação (ex.: *Quelimane, Zambézia*).
- **Endereço Físico:** Bairro e avenida principal da igreja.
- **Descrição / Lema:** Breve apresentação da congregação.

### 🔹 Passo 2: Criar Conta do Pastor / Administrador Principal
- **Nome Completo:** Nome do Pastor Sénior ou Administrador do Sistema.
- **E-mail de Acesso:** O seu e-mail pessoal/profissional para fazer login no portal.
- **Telefone:** Contacto telefónico do administrador.
- **Palavra-passe:** Defina uma palavra-passe segura com pelo menos 6 caracteres.
*(Esta conta terá a função de Super Administrador com acesso total a todas as funcionalidades).*

### 🔹 Passo 3: Estrutura Pastoral & Personalização Visual
- **Nome da 1ª Zona Pastoral (Opcional):** Introduza o nome da zona pastoral inicial (ex.: *Zona Central*, *Zona A* ou *Zona Quelimane Norte*). Isso cria imediatamente a estrutura para depois adicionar supervisões e células.
- **Cores do Tema:** Ajuste a cor primária (Laranja `#f97316`), secundária e de destaque para corresponder à marca da sua congregação.

### 🔹 Passo 4: Conclusão & Acesso ao Portal
- Clique no botão **"Aceder ao Portal Agora"**.
- O sistema bloqueia automaticamente o assistente `/setup` para evitar acessos não autorizados e redireciona-o para a página de login.

---

## 🔑 4. Guião de Primeiros Passos Pós-Instalação

Assim que fizer login com a conta de Pastor/Admin criada no assistente, recomendamos a seguinte sequência para colocar a congregação a funcionar:

1. **Criar a Estrutura Pastoral:**
   - Aceda a **Menu ➔ Gestão Eclesiástica ➔ Zonas Ministeriais**.
   - Registe as Zonas Ministeriais e atribua um Pastor de Zona.
   - Em cada Zona, crie as **Supervisões** e atribua um Supervisor.
   - Em cada Supervisão, crie as **Células** e atribua um Líder de Célula.

2. **Cadastrar Membros e Visitantes:**
   - Aceda a **Menu ➔ Membros ➔ Cadastrar Novo Membro**.
   - Atribua cada membro à respetiva Célula.

3. **Registo de Cultos e Frequência:**
   - Aceda a **Menu ➔ Cultos & Eventos ➔ Registar Culto**.
   - Selecione o tipo de culto (Celebração de Domingo ou Culto de Doutrina/Ensino por Zonas).
   - Introduza o tema, pregador e dados de frequência de adultos, crianças e salvações.

4. **Emissão de Relatórios & PDFs:**
   - Aceda aos relatórios globais, relatórios por célula ou baixe o PDF resumido do culto pronto para impressão com a identificação da sua congregação.

---

## 🛠️ Resolução de Problemas Frequentes

* **O assistente `/setup` diz "O sistema já foi configurado":**
  - Significa que o sistema já completou a instalação. Para reconfigurar dados da congregação sem apagar nada, aceda a **Menu ➔ Definições ➔ Definições Gerais**.
* **Erro de permissões de ficheiros no servidor:**
  - Execute `chmod -R 775 storage bootstrap/cache` no terminal Linux.
* **As imagens e logótipos não aparecem:**
  - Execute `php artisan storage:link` no terminal para garantir a ligação da pasta de imagens públicas.
