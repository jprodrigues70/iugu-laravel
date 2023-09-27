# iugu-laravel
Interface de comunicação com a API do Iugu para Laravel

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
