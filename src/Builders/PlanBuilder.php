<?php

namespace IuguLaravel\Builders;

use IuguLaravel\Builders\Contracts\Builder;

/**
 * Class PlanBuilder
 *
 * En:
 * The `PlanBuilder` class is responsible for building and interacting with Iugu plans.
 * It allows you to create, edit, remove, search, search by identifier and list plans.
 *
 * Pt:
 * A classe `PlanBuilder` é responsável por construir e interagir com planos da Iugu.
 * Ela permite criar, editar, remover, buscar e buscar pelo identificador e listar planos.
 *
 * @package IuguLaravel\Builders
 */
class PlanBuilder extends Builder
{
    protected string $endpoint = "/v1/plans";

    protected array $requiredFields = [
        'create' => [
            "name",
            "identifier",
            "interval",
            "interval_type",
            "value_cents",
        ]
    ];
}
