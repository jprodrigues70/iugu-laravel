<?php

namespace IuguLaravel\Builders;

use IuguLaravel\Builders\Contracts\Builder;

/**
 * Class CustomerBuilder
 *
 * En:
 * The `CustomerBuilder` class is responsible for building and interacting with Iugu customers.
 * It allows you to create, edit, remove, search, and list customers.
 *
 * Pt:
 * A classe `CustomerBuilder` é responsável por construir e interagir com clientes da Iugu.
 * Ela permite criar, alterar, remover, buscar e listar clientes.
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
