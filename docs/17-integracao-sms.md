# Integração do Serviço HttpSMS & Fluxo de Feedback de Visitas

Este documento descreve a arquitetura, configuração e fluxos de trabalho do serviço de SMS integrado ao Portal Life Church, utilizando o provedor **httpSMS** para automatização da comunicação com líderes de célula.

## 1. Visão Geral

A integração do **httpSMS** permite que o sistema envie notificações SMS automatizadas diretamente para os números de celular dos líderes de célula. O principal caso de uso implementado é a **Notificação de Novos Visitantes**, onde um líder é alertado assim que um novo visitante é associado à sua célula, contendo informações fundamentais para contacto imediato.

Adicionalmente, foi implementado um **Fluxo de Feedback**, permitindo que os líderes registem o status de contacto do visitante e adicionem observações diretamente da Ficha Guia (dashboard de presença).

---

## 2. Configuração do Provedor (`httpSMS`)

### Variáveis de Ambiente (`.env`)
Adicione as seguintes credenciais ao arquivo `.env`:

```env
# SMS Configuration (httpsms)
SMS_DRIVER=httpsms
HTTPSMS_KEY=sua_chave_api_aqui
HTTPSMS_FROM=+258XXXXXXXXX
```

### Estrutura de Configuração (`config/services.php`)
A chave de configuração foi registrada em `config/services.php`:

```php
'httpsms' => [
    'key' => env('HTTPSMS_KEY'),
    'from' => env('HTTPSMS_FROM'),
],
```

---

## 3. Detalhes de Implementação

### 3.1 Provedor SMS (`HttpsmsProvider.php`)
Localizado em `app/Services/Sms/HttpsmsProvider.php`. Ele implementa a interface `SmsProviderInterface`:

*   **Método `send(string $phone, string $message)`**: Envia uma requisição HTTP POST para `https://api.httpsms.com/v1/messages/send` contendo os cabeçalhos de autenticação e payload JSON. Formata automaticamente o número do destinatário para o formato internacional requerido.
*   **Método `sendBulk(array $phones, string $message)`**: Permite disparos em lote sequenciais.

### 3.2 Registro no `SmsService.php`
O resolvedor de provedores foi atualizado para retornar a instância correta do driver `httpsms`:

```php
switch ($driver) {
    case 'log':
        return new LogSmsProvider();
    case 'mocean':
        return new MoceanSmsProvider();
    case 'httpsms':
        return new HttpsmsProvider();
    default:
        return new LogSmsProvider();
}
```

---

## 4. Fluxo Automatizado de Visitas

### 4.1 Gatilho Automatizado via Eloquent (`Visitor.php`)
No model `Visitor`, o método `booted` intercepta os eventos `created` e `saved` para detectar novas atribuições de células:

```php
protected static function booted()
{
    static::saved(function ($visitor) {
        if ($visitor->wasChanged('cell_id') && $visitor->cell_id) {
            $visitor->notifyCellLeaderAboutAssignment();
        }
    });

    static::created(function ($visitor) {
        if ($visitor->cell_id) {
            $visitor->notifyCellLeaderAboutAssignment();
        }
    });
}
```

### 4.2 Notificação por SMS ao Líder
Quando acionado, o sistema envia a seguinte mensagem para o líder da célula:

> *"Paz Lider, o visitante [Nome] ([Telefone]) do bairro [Bairro] foi atribuido a sua celula ([Nome da Celula]). Faca o contacto e de o feedback no sistema."*

---

## 5. Interface de Feedback do Líder (Ficha Guia)

Para fechar o ciclo de comunicação, o modal de detalhes do visitante na Ficha Guia da célula foi estendido:
1.  **Formulário de Feedback integrado**: Permite ao líder atualizar o status de contacto (`Pendente`, `Contatado`, `Integrado`, `Sem Interesse`) e adicionar observações de acompanhamento.
2.  **Segurança e Acesso**: Rota protegida `/admin/cells/{cell}/visitors/{visitor}/feedback` que garante que apenas o líder da célula associada (ou supervisores/administradores) possa submeter o feedback.

---

## 6. Diagnóstico e Testes

Para testar o funcionamento do serviço diretamente pelo terminal, utilize o Laravel Tinker:

```bash
php artisan tinker --execute="var_dump(app(App\Services\Sms\SmsService::class)->send('258862134230', 'Teste de integração do Portal Life Church via httpSMS bem sucedido!'))"
```
