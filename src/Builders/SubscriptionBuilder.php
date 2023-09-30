<?php

namespace IuguLaravel\Builders;

use IuguLaravel\Builders\Contracts\Builder;

/**
 * Class Subscription
 *
 * En:
 * The `Subscription` class is responsible for building and interacting with Iugu subscriptions.
 *
 * Pt:
 * A classe `Subscription` é responsável por construir e interagir com assinaturas da Iugu.
 *
 * @package IuguLaravel\Builders
 */
class SubscriptionBuilder extends Builder
{
    protected string $endpoint = "/v1/subscriptions";

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
