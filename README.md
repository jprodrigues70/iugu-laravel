# iugu-laravel

Interface de comunicação com a API do Iugu para Laravel

## Desenvolvimento com Docker

### 1) Configure a chave da Iugu

Use o exemplo abaixo para criar seu arquivo de ambiente local:

```bash
cp .env.docker.example .env
```

Edite o `.env` e informe sua `IUGU_APIKEY`.

### 2) Suba o ambiente de desenvolvimento

```bash
docker compose up -d --build
```

### 3) Instale as dependências da lib

```bash
docker compose run --rm dev composer install
```

### 4) Entrar no shell do container

```bash
docker compose run --rm dev bash
```

### 5) Comandos úteis

```bash
# Validar composer.json
docker compose run --rm dev composer validate --no-check-publish

# Atualizar dependências
docker compose run --rm dev composer update

# Rodar o serviço de CI/teste (install + validate)
docker compose --profile test run --rm ci
```

# exemplo de uso até o momento:

```
$user = User::find(1);
$invoice = new InvoiceBuilder();
$result = $invoice
    ->setEmail($user->email)
    ->setDueDate(Carbon::now()->addDays(3)->format("Y-m-d"))
    ->setPayer(
        name: $user->name,
        documentNumber: $user->cpf
    )
    ->setItem(
        description: 'Fatura de teste',
        quantity: 2,
        priceCents: 100
    )
    ->setLocalPersistenceConfig(
        model: new Invoice(),
        map: [
            'gatewayColumn' => 'service',
            'id' => 'service_id',
            'total_cents' => 'price'
        ],
        data: [
            'user_id' => $user->id,
        ]
    )
    ->create();
```

## Criação de fatura (payload completo)

```php
$invoice = (new InvoiceBuilder())
    ->setEmail('cliente@empresa.com')
    ->setDueDate('2026-03-20')
    ->setCcEmails('financeiro@empresa.com,contato@empresa.com')
    ->setEnsureWorkdayDueDate(true)
    ->setExpiresIn('5')
    ->setBankSlipExtraDue('2')
    ->setPayableWith(['all'])
    ->setItem(description: 'Plano Pro', quantity: 1, priceCents: 15900)
    ->setPayer(
        name: 'Maria Silva',
        documentNumber: '12345678901',
        extra: [
            'email' => 'cliente@empresa.com',
            'phone_prefix' => '11',
            'phone' => '999999999',
        ]
    )
    ->setPayerAddress([
        'zip_code' => '01310100',
        'street' => 'Av Paulista',
        'number' => '1000',
        'district' => 'Bela Vista',
        'city' => 'Sao Paulo',
        'state' => 'SP',
        'country' => 'Brasil',
        'complement' => 'Conj 101',
    ])
    ->setCustomerId('CUSTOMER_ID')
    ->setReturnUrl('https://empresa.com/pagamento/sucesso')
    ->setExpiredUrl('https://empresa.com/pagamento/expirado')
    ->setNotificationUrl('https://empresa.com/webhooks/iugu')
    ->setIgnoreCanceledEmail(true)
    ->setIgnoreDueEmail(false)
    ->setFines(true)
    ->setLatePaymentFine(2)
    ->setPerDayInterest(true)
    ->setPerDayInterestValue(1)
    ->setDiscountCents(500)
    ->setOrderId('ORDER-123')
    ->setExternalReference('REF-123')
    ->setMaxInstallmentsValue(6)
    ->setSoftDescriptorLight('EMPRESA')
    ->addCustomVariable('pedido', 'ORDER-123')
    ->addEarlyPaymentDiscount(days: 5, percent: 3)
    ->addSplit([
        'recipient_account_id' => 'ACCOUNT_SPLIT_ID',
        'percent' => 5.5,
        'permit_aggregated' => true,
    ])
    ->setAutomaticPix([
        'journey' => 3,
        'frequency' => 'monthly',
        'recurrence_beginning' => '2026-04-01',
        'contract_number' => 'CTR-001',
    ])
    ->addPixAdditionalInfo('produto', 'Plano Pro')
    ->create();
```

## Reembolso de fatura

Reembolso total por ID da fatura:

```php
$result = (new InvoiceBuilder())
    ->refundById('EBB161AAB22849BFA047D5F1DF55AEE9');
```

Reembolso parcial por ID da fatura:

```php
$result = (new InvoiceBuilder())
    ->partialRefundById('EBB161AAB22849BFA047D5F1DF55AEE9', 500);
```

Reembolso da fatura já carregada no builder:

```php
$result = (new InvoiceBuilder('EBB161AAB22849BFA047D5F1DF55AEE9'))
    ->refund();
```

## Cancelamento de fatura

Cancelamento por ID da fatura:

```php
$result = (new InvoiceBuilder())
    ->cancelById('EBB161AAB22849BFA047D5F1DF55AEE9');
```

Cancelamento da fatura já carregada no builder:

```php
$result = (new InvoiceBuilder('EBB161AAB22849BFA047D5F1DF55AEE9'))
    ->cancel();
```

## Captura de fatura

Captura por ID da fatura:

```php
$result = (new InvoiceBuilder())
    ->captureById('EBB161AAB22849BFA047D5F1DF55AEE9');
```

Captura da fatura já carregada no builder:

```php
$result = (new InvoiceBuilder('EBB161AAB22849BFA047D5F1DF55AEE9'))
    ->capture();
```
