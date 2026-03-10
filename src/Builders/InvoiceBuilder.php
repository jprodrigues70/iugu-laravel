<?php

namespace IuguLaravel\Builders;

use DateTimeImmutable;
use Exception;
use IuguLaravel\Builders\Contracts\Builder;
use IuguLaravel\Http\ResponseHttp;

/**
 * Class InvoiceBuilder
 *
 * En:
 * The `InvoiceBuilder` class is responsible for building and interacting with Iugu invoices.
 * It allows you to create, cancel, capture, refund, and duplicate invoices, as well as set invoice items and payer information.
 *
 * Pt:
 * A classe `InvoiceBuilder` é responsável por construir e interagir com faturas da Iugu.
 * Ela permite criar, cancelar, capturar, reembolsar e duplicar faturas, além de definir itens da fatura e informações do pagador.
 *
 * @package IuguLaravel\Builders
 */
class InvoiceBuilder extends Builder
{
    private const PAYABLE_WITH_VALUES = ['all', 'credit_card', 'bank_slip', 'pix'];

    protected string $endpoint = "/v1/invoices";

    protected array $requiredFields = [
        'create' => [
            "due_date",
            "email",
            "items",
        ]
    ];

    /**
     * En:
     * Define an item to be added to the invoice, including its description, quantity, and price in cents.
     *
     * Pt:
     * Define um item a ser adicionado à fatura, incluindo sua descrição, quantidade e preço em centavos.
     *
     */
    public function setItem(string $description, int $quantity, int $priceCents): self
    {
        if ($quantity < 1) {
            throw new Exception('quantity must be greater than or equal to 1');
        }

        if ($priceCents < 100) {
            throw new Exception('price_cents must be greater than or equal to 100');
        }

        $this->data->items[] = [
            "description" => $description,
            "quantity" => $quantity,
            "price_cents" => $priceCents
        ];

        return $this;
    }

    /**
     * Define all invoice items at once.
     *
     * @param array<int, array<string, mixed>> $items
     */
    public function setItems(array $items): self
    {
        $this->data->items = [];

        foreach ($items as $item) {
            $this->setItem(
                description: (string) ($item['description'] ?? ''),
                quantity: (int) ($item['quantity'] ?? 0),
                priceCents: (int) ($item['price_cents'] ?? 0)
            );
        }

        return $this;
    }

    /**
     * Set payer information for the invoice.
     *
     * En:
     * Define payer information for the invoice, including the payer's name and document number (CPF/CNPJ).
     *
     * Pt:
     * Define informações do pagador para a fatura, incluindo o nome e o número do documento do pagador (CPF/CNPJ).
     *
     */
    public function setPayer(string|array $name, ?string $documentNumber = null, array $extra = []): self
    {
        if (is_array($name)) {
            $this->data->payer = $name;
            return $this;
        }

        if (!$documentNumber) {
            throw new Exception('documentNumber is required when setting payer by scalar arguments');
        }

        $this->data->payer = [
            "name" => $name,
            "cpf_cnpj" => $documentNumber
        ];

        if (!empty($extra)) {
            $this->data->payer = array_merge($this->data->payer, $extra);
        }

        return $this;
    }

    /**
     * Define payer address while preserving existing payer fields.
     *
     * @param array<string, mixed> $address
     */
    public function setPayerAddress(array $address): self
    {
        $payer = (array) ($this->data->payer ?? []);
        $payer['address'] = $address;
        $this->data->payer = $payer;

        return $this;
    }

    /**
     * @param array<int, string> $methods
     */
    public function setPayableWith(array $methods): self
    {
        foreach ($methods as $method) {
            if (!in_array($method, self::PAYABLE_WITH_VALUES, true)) {
                throw new Exception("Invalid payable_with method: $method");
            }
        }

        $this->data->payable_with = array_values(array_unique($methods));
        return $this;
    }

    /**
     * @param array<string, mixed> $split
     */
    public function addSplit(array $split): self
    {
        if (empty($split['recipient_account_id'])) {
            throw new Exception('recipient_account_id is required in split');
        }

        $this->data->splits[] = $split;
        return $this;
    }

    /**
     * @param array<int, array<string, mixed>> $splits
     */
    public function setSplits(array $splits): self
    {
        $this->data->splits = [];

        foreach ($splits as $split) {
            $this->addSplit($split);
        }

        return $this;
    }

    public function addCustomVariable(string $name, string $value): self
    {
        if (mb_strlen($name) > 50) {
            throw new Exception('custom_variables.name max length is 50');
        }

        if (mb_strlen($value) > 200) {
            throw new Exception('custom_variables.value max length is 200');
        }

        $this->data->custom_variables[] = [
            'name' => $name,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * @param array<int, array{name:string,value:string}> $variables
     */
    public function setCustomVariables(array $variables): self
    {
        $this->data->custom_variables = [];

        foreach ($variables as $variable) {
            $this->addCustomVariable(
                name: (string) ($variable['name'] ?? ''),
                value: (string) ($variable['value'] ?? '')
            );
        }

        return $this;
    }

    public function addEarlyPaymentDiscount(int $days, ?float $percent = null, ?int $valueCents = null): self
    {
        if (($percent === null && $valueCents === null) || ($percent !== null && $valueCents !== null)) {
            throw new Exception('Provide only one of percent or value_cents for early payment discount');
        }

        $discount = ['days' => $days];

        if ($percent !== null) {
            $discount['percent'] = $percent;
        }

        if ($valueCents !== null) {
            $discount['value_cents'] = $valueCents;
        }

        $this->data->early_payment_discounts[] = $discount;
        return $this;
    }

    /**
     * @param array<int, array<string, mixed>> $discounts
     */
    public function setEarlyPaymentDiscounts(array $discounts): self
    {
        $this->data->early_payment_discounts = [];

        foreach ($discounts as $discount) {
            $this->addEarlyPaymentDiscount(
                days: (int) ($discount['days'] ?? 0),
                percent: array_key_exists('percent', $discount) ? (float) $discount['percent'] : null,
                valueCents: array_key_exists('value_cents', $discount) ? (int) $discount['value_cents'] : null
            );
        }

        return $this;
    }

    /**
     * @param array<string, mixed> $automaticPix
     */
    public function setAutomaticPix(array $automaticPix): self
    {
        $required = ['journey', 'frequency', 'recurrence_beginning', 'contract_number'];

        foreach ($required as $field) {
            if (empty($automaticPix[$field])) {
                throw new Exception("automatic_pix.$field is required");
            }
        }

        $this->data->automatic_pix = $automaticPix;
        return $this;
    }

    public function addPixAdditionalInfo(string $name, string $value): self
    {
        if (mb_strlen($name) > 50) {
            throw new Exception('pix_additional_info.name max length is 50');
        }

        if (mb_strlen($value) > 200) {
            throw new Exception('pix_additional_info.value max length is 200');
        }

        $current = (array) ($this->data->pix_additional_info ?? []);

        if (count($current) >= 5) {
            throw new Exception('pix_additional_info accepts a maximum of 5 items');
        }

        $this->data->pix_additional_info[] = [
            'name' => $name,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * @param array<int, array{name:string,value:string}> $items
     */
    public function setPixAdditionalInfo(array $items): self
    {
        if (count($items) > 5) {
            throw new Exception('pix_additional_info accepts a maximum of 5 items');
        }

        $this->data->pix_additional_info = [];

        foreach ($items as $item) {
            $this->addPixAdditionalInfo(
                name: (string) ($item['name'] ?? ''),
                value: (string) ($item['value'] ?? '')
            );
        }

        return $this;
    }

    /**
     * Merge payload fields directly when needed for advanced options.
     *
     * @param array<string, mixed> $payload
     */
    public function fill(array $payload): self
    {
        foreach ($payload as $key => $value) {
            $this->data->{$key} = $value;
        }

        return $this;
    }

    public function create(): ResponseHttp
    {
        $this->validateCreatePayload();
        return parent::create();
    }

    private function validateCreatePayload(): void
    {
        $this->validateDueDate();
        $this->validateExpirationRules();
        $this->validatePayerRules();
    }

    private function validateDueDate(): void
    {
        if (!isset($this->data->due_date)) {
            return;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', (string) $this->data->due_date);

        if (!$date || $date->format('Y-m-d') !== $this->data->due_date) {
            throw new Exception('due_date must be in Y-m-d format');
        }
    }

    private function validateExpirationRules(): void
    {
        if (isset($this->data->expires_in)) {
            $expiresIn = (int) $this->data->expires_in;
            if ($expiresIn < 0 || $expiresIn > 120) {
                throw new Exception('expires_in must be between 0 and 120');
            }
        }

        if (isset($this->data->bank_slip_extra_due)) {
            $extraDue = (int) $this->data->bank_slip_extra_due;
            if ($extraDue < 1 || $extraDue > 120) {
                throw new Exception('bank_slip_extra_due must be between 1 and 120');
            }
        }
    }

    private function validatePayerRules(): void
    {
        $payer = (array) ($this->data->payer ?? []);

        if (!empty($payer['cpf_cnpj']) && empty($payer['name'])) {
            throw new Exception('payer.name is required when payer.cpf_cnpj is informed');
        }

        if (isset($payer['name']) && mb_strlen((string) $payer['name']) > 140) {
            throw new Exception('payer.name max length is 140');
        }
    }
}
