<?php

namespace IuguLaravel\Http;

use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseHttp
 *
 * En:
 * The `ResponseHttp` class represents an HTTP response received from an external API. It encapsulates the response status,
 * content, and provides access to a local model for further processing.
 *
 * Pt:
 * A classe `ResponseHttp` representa uma resposta HTTP recebida de uma API externa. Ela encapsula o status da resposta,
 * seu conteúdo e fornece acesso a um modelo local para processamento adicional.
 *
 * @package Escavador\Libraries\IuguPhp\Src\Http
 */
class ResponseHttp
{
    /**
     * En:
     * The HTTP status code of the response.
     *
     * Pt:
     * O código de status HTTP da resposta.
     *
     * @var integer
     */
    protected int $status;

    /**
     * En:
     * The content of the HTTP response represented as an object.
     *
     * Pt:
     * O conteúdo da resposta HTTP representado como um objeto.
     *
     * @var object $content
     */
    protected object $content;

    /**
     * En:
     * A local model associated with the response for further processing, such as data mapping or database storage.
     *
     * Pt:
     * Um modelo local associado à resposta para processamento adicional, como mapeamento de dados ou armazenamento em banco de dados.
     *
     * @var Model $localModel
     */
    protected Model $localModel;

    /**
     * ResponseHttp constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->status = $response->getStatusCode();
        $this->content = json_decode($response->getBody()->getContents());
    }

    /**
     * Magic method for getting class properties.
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->$name;
    }

    /**
     * Magic method for setting class properties.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, mixed $value): void
    {
        $this->$name = $value;
    }
}
