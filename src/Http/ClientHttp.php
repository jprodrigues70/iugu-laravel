<?php

namespace IuguLaravel\Http;

use GuzzleHttp\Client;
use InvalidArgumentException;

/**
 * Class ClientHttp
 *
 * En:
 * The `ClientHttp` class is responsible for making HTTP requests to the Iugu API using the Guzzle HTTP client library.
 * It provides methods for sending GET, POST, PUT, and DELETE requests and handling responses.
 *
 * Pt:
 * A classe `ClientHttp` é responsável por fazer requisições HTTP para a API da Iugu usando a biblioteca de cliente HTTP Guzzle.
 * Ela fornece métodos para enviar requisições GET, POST, PUT e DELETE e lidar com as respostas.
 *
 * @package IuguLaravel\Http
 */
class ClientHttp
{
    /**
     * En:
     * The Guzzle HTTP client instance used for making HTTP requests.
     *
     * Pt:
     * A instância do cliente HTTP Guzzle usada para fazer requisições HTTP.
     */
    protected Client $guzzleClient;

    /**
     * Cabeçalhos padrão enviados em todas as requisições.
     *
     * @var array<string, string>
     */
    protected array $defaultHeaders;

    /**
     * ClientHttp constructor.
     *
     * En:
     * Creates a new instance of the `ClientHttp` class.
     *
     * Pt:
     * Cria uma nova instância da classe `ClientHttp`.
     */
    public function __construct(?Client $guzzleClient = null, ?string $apiKey = null, string $baseUri = 'https://api.iugu.com')
    {
        $apiKey = $apiKey ?? getenv('IUGU_APIKEY') ?: null;

        if (!$apiKey) {
            throw new InvalidArgumentException('IUGU_APIKEY is not configured.');
        }

        $this->defaultHeaders = [
            'Authorization' => 'Basic ' . base64_encode($apiKey . ':'),
            'Accept' => 'application/json',
        ];

        $this->guzzleClient = $guzzleClient ?? new Client([
            'base_uri' => $baseUri,
            'headers' => $this->defaultHeaders,
        ]);
    }

    /**
     * En: Send an HTTP request using Guzzle.
     *
     * Pt: Envia uma requisição HTTP usando Guzzle.
     *
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $headers
     * @param integer|null $timeout
     * @return ResponseHttp
     */
    private function sendRequest(string $method, string $path, array $data = [], array $headers = [], int $timeout = null): ResponseHttp
    {
        $options = [
            'headers' => array_merge($this->defaultHeaders, $headers),
        ];

        if (!empty($data)) {
            $options['json'] = $data;
        }

        if ($timeout) {
            $options['timeout'] = $timeout;
        }

        $response = $this->guzzleClient->request($method, $path, $options);
        return new ResponseHttp($response);
    }

    /**
     * En:
     * Sends a GET request to the specified path with optional headers and a timeout, and returns the response.
     *
     * Pt:
     * Envia uma requisição GET para o caminho especificado com cabeçalhos opcionais e um tempo limite, e retorna a resposta.
     */
    public function get(string $path, array $headers = [], int $timeout = null): ResponseHttp
    {
        return $this->sendRequest('GET', $path, [], $headers, $timeout);
    }

    /**
     * En:
     * Sends a POST request to the specified path with optional data and headers, and returns the response.
     *
     * Pt:
     * Envia uma requisição POST para o caminho especificado com dados e cabeçalhos opcionais, e retorna a resposta.
     *
     * @param string $path
     * @param array $data
     * @param array $headers
     * @param integer|null $timeout
     * @return ResponseHttp
     */
    public function post(string $path, array $data = [], array $headers = [], int $timeout = null): ResponseHttp
    {
        return $this->sendRequest('POST', $path, $data, $headers, $timeout);
    }

    /**
     * En:
     * Sends a PUT request to the specified path with optional data and headers, and returns the response.
     *
     * Pt:
     * Envia uma requisição PUT para o caminho especificado com dados e cabeçalhos opcionais, e retorna a resposta.
     *
     * @param string $path
     * @param array $data
     * @param array $headers
     * @param integer|null $timeout
     * @return ResponseHttp
     */
    public function put(string $path, array $data = [], array $headers = [], int $timeout = null): ResponseHttp
    {
        return $this->sendRequest('PUT', $path, $data, $headers, $timeout);
    }

    /**
     * En:
     * Sends a DELETE request to the specified path with optional headers and a timeout, and returns the response.
     *
     * Pt:
     * Envia uma requisição DELETE para o caminho especificado com cabeçalhos opcionais e um tempo limite, e retorna a resposta.
     *
     * @param string $path
     * @param array $headers
     * @param integer|null $timeout
     * @return ResponseHttp
     */
    public function delete(string $path, array $headers = [], int $timeout = null): ResponseHttp
    {
        return $this->sendRequest('DELETE', $path, [], $headers, $timeout);
    }
}
