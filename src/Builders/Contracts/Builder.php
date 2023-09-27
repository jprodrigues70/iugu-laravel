<?php

namespace IuguLaravel\Builders\Contracts;

use stdClass;
use AllowDynamicProperties;
use Illuminate\Support\Str;
use IuguLaravel\Http\ClientHttp;
use IuguLaravel\Http\ResponseHttp;
use Illuminate\Database\Eloquent\Model;

#[AllowDynamicProperties]
abstract class Builder
{
    /**
     * En:
     * The data property stores retrieved data from external sources, such as APIs.
     *
     * Pt:
     * A propriedade $data armazena os dados recuperados de fontes externas, como APIs.
     *
     */
    protected object $data;

    /**
     * En:
     * The persistence property is used to configure how data will be persisted locally.
     *
     * Pt:
     * A propriedade $persistence é usada para configurar como os dados serão persistidos localmente.
     *
     */
    protected object $persistence;

    /**
     * En:
     * The api property is an instance of the ClientHttp class, typically used for making HTTP requests.
     *
     * Pt:
     * A propriedade $api é uma instância da classe ClientHttp, normalmente usada para fazer requisições HTTP.
     */
    protected ClientHttp $api;

    /**
     * En:
     * The endpoint property represents the API endpoint for data retrieval.
     *
     * Pt:
     * A propriedade $endpoint representa o endpoint da API para a recuperação de dados.
     */
    protected string $endpoint;

    /**
     * En:
     * Constructor for the Builder class.
     *
     * Pt:
     * Construtor da classe Builder.
     *
     * @param string|null $id
     * @return void
     */
    public function __construct(?string $id = null)
    {
        $this->api = new ClientHttp();
        $this->data = $id ? $this->fetch($id)->content : new stdClass;
        $this->persistence = new stdClass;
    }

    /**
     * En:
     * Static factory method that creates an instance of the class and calls a method on it.
     *
     * Pt:
     * Método de fábrica estático que cria uma instância da classe e chama um método nela.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return (new static)->$name(...$arguments);
    }

    /**
     * En:
     * Magic method that handles dynamic method calls.
     *
     * Pt:
     * Método mágico que lida com chamadas de método dinâmicas.
     *
     * @param string $method
     * @param array $args
     * @return self
     * @throws \Exception
     */
    public function __call(string $method, array $args): self
    {
        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } elseif (count($args)) {
            $column = str_replace('set_', '', Str::snake($method));
            $this->data->{$column} = $args[0];
        } else {
            throw new \Exception("You need to pass some args in $method");
        }

        return $this;
    }

    /**
     * En:
     * Fetches data from an API endpoint.
     *
     * Pt:
     * Recupera dados de um endpoint de API.
     *
     */
    protected function fetch(string $id): ResponseHttp
    {
        return $this->api->get(path: "{$this->endpoint}/$id");
    }


    /**
     * En:
     * Configures local data persistence settings.
     *
     * Pt:
     * Configura as configurações de persistência local de dados.
     *
     * @param Model $model
     * @param array $map
     * @param array $data
     * @return self
     */
    public function setLocalPersistenceConfig(Model $model, array $map = [], array $data = []): self
    {
        $this->persistence->model = $model;
        $this->persistence->map = $map;
        $this->persistence->data = $data;
        return $this;
    }

    /**
     * En:
     * Persists data locally.
     *
     * Pt:
     * Persiste dados localmente.
     *
     * @param object|null $attributes
     * @return Model
     */
    public function saveLocally(?object $attributes = null): Model
    {
        $model = $this->persistence->model;
        $data = $attributes ?? $this->data;
        $data->gatewayColumn = 'iugu';

        foreach ($this->persistence->map as $key => $col) {
            $model->$col = $data->$key;
        }

        foreach ($this->persistence->data as $key => $value) {
            $model->$key = $value;
        }

        $model->save();

        return $model;
    }
}
