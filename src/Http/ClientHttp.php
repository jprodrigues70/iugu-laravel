<?php

namespace IuguLaravel\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
    protected $guzzleClient;

    /**
     * En:
     * The response from the last HTTP request made by the client.
     *
     * Pt:
     * A resposta da última requisição HTTP feita pelo cliente.
     */
    protected $response = null;

    /**
     * ClientHttp constructor.
     *
     * En:
     * Creates a new instance of the `ClientHttp` class.
     *
     * Pt:
     * Cria uma nova instância da classe `ClientHttp`.
     */
    public function __construct()
    {
        $this->guzzleClient = new Client([
            'base_uri' => 'https://api.iugu.com',
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(getenv('IUGU_APIKEY') . ':'),
                'Accept' => 'application/json',
            ],
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
     * @throws RequestException
     */
    private function sendRequest(string $method, string $path, array $data = [], array $headers = [], int $timeout = null): ResponseHttp
    {
        try {
            $options = [
                'headers' => $headers,
            ];

            if (!empty($data)) {
                $options['json'] = $data;
            }

            if ($timeout) {
                $options['timeout'] = $timeout;
            }

            $response = $this->guzzleClient->request($method, $path, $options);
            return new ResponseHttp($response);
        } catch (RequestException $e) {
            throw $e;
        }
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
