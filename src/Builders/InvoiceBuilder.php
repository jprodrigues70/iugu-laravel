<?php

namespace IuguLaravel\Builders;

use IuguLaravel\Builders\Contracts\Builder;

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
    protected string $endpoint = "/v1/invoices";

    protected array $requiredFields = [
        'create' => [
            "due_date",
            "email",
            "items",
            "payer"
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
        $this->data->items[] = [
            "description" => $description,
            "quantity" => $quantity,
            "price_cents" => $priceCents
        ];
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
    public function setPayer(string $name, string $documentNumber): self
    {
        $this->data->payer = [
            "name" => $name,
            "cpf_cnpj" => $documentNumber
        ];
        return $this;
    }
}
