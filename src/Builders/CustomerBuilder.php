<?php

namespace IuguLaravel\Builders;

use IuguLaravel\Builders\Contracts\Builder;

/**
 * Class CustomerBuilder
 *
 * En:
 * The `CustomerBuilder` class is responsible for building and interacting with Iugu invoices.
 * It allows you to create, cancel, capture, refund, and duplicate invoices, as well as set invoice items and payer information.
 *
 * Pt:
 * A classe `CustomerBuilder` é responsável por construir e interagir com faturas da Iugu.
 * Ela permite criar, cancelar, capturar, reembolsar e duplicar faturas, além de definir itens da fatura e informações do pagador.
 *
 * @package IuguLaravel\Builders
 */
class CustomerBuilder extends Builder
{
    protected string $endpoint = "/v1/customers";

    protected array $requiredFields = [
        'create' => [
            "email",
            "name"
        ]
    ];
}
