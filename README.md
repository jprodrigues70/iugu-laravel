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
