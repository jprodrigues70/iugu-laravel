<?php

namespace IuguLaravel\Builders;

use Exception;
use IuguLaravel\Builders\Contracts\Builder;
use IuguLaravel\Http\ResponseHttp;

/**
 * Class FaturaBuilder
 *
 * En:
 * The `FaturaBuilder` class is responsible for building and interacting with Iugu invoices.
 * It allows you to create, cancel, capture, refund, and duplicate invoices, as well as set invoice items and payer information.
 *
 * Pt:
 * A classe `FaturaBuilder` é responsável por construir e interagir com faturas da Iugu.
 * Ela permite criar, cancelar, capturar, reembolsar e duplicar faturas, além de definir itens da fatura e informações do pagador.
 *
 * @package IuguLaravel\Builders
 */
class InvoiceBuilder extends Builder
{
    protected string $endpoint = "/v1/invoices";

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

    /**
     * En:
     * Create a new invoice based on the provided data.
     *
     * Pt:
     * Crie uma nova fatura com base nos dados fornecidos.
     *
     * @link https://dev.iugu.com/reference/criar-fatura
     */
    public function create(): ResponseHttp
    {
        $requiredFields = [
            "due_date",
            "email",
            "items",
            "payer"
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->data->{$field})) {
                throw new Exception("$field is required");
            }
        }

        $result = $this->api->post(path: $this->endpoint, data: get_object_vars($this->data));

        if ($result?->status === 200 && $result?->content && $this->persistence?->model) {
            $result->localModel = $this->saveLocally(attributes: $result->content);
        }

        return $result;
    }
}
