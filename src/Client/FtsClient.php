<?php

namespace Drupal\fts\Client;

use Drupal;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\fts\FtsClientInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\fts\Model\ApiResponse;

class FtsClient implements FtsClientInterface
{

    /**
     * An http client.
     *
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * Base URI for the service.
     *
     * @var string
     */
    protected $base_uri;

    /**
     * Token for authentication.
     *
     * @var string
     */
    protected $token;

    /**
     * Secret for authentication.
     *
     * @var string
     */
    protected $secret;

    /**
     * Messenger service.
     *
     * @var MessengerInterface
     */
    protected $messenger;

    /**
     * Constructor.
     *
     * @param ClientInterface $http_client
     *   The HTTP client.
     * @param MessengerInterface $messenger
     *   The messenger service for displaying messages.
     */
    public function __construct(ClientInterface $http_client, MessengerInterface $messenger)
    {
        $this->httpClient = $http_client;
        $this->messenger = $messenger; // Now correctly injecting the messenger service
        $this->base_uri = 'http://host.docker.internal:8081'; // Update to your actual service URL
        $this->token = 'seu-token-aqui'; // Define your token
        $this->secret = 'seu-segredo-aqui'; // Define your secret
    }

    /**
     * Connect to the API.
     *
     * @param string $method
     *   The HTTP method (GET, POST, etc).
     * @param string $endpoint
     *   The endpoint to connect to.
     * @param array $query
     *   Query string parameters.
     * @param array $body
     *   The request body.
     *
     * @return string|false
     *   The response body or FALSE on failure.
     */
    public function connect($method, $endpoint, $query, $body)
    {
        try {
            // Faz a requisição HTTP usando o Guzzle
            $response = $this->httpClient->{$method}(
                $this->base_uri . $endpoint,
                $this->buildOptions($query, $body)
            );

            // Decodifica a resposta JSON
            $data = json_decode($response->getBody()->getContents(), true);

            // Depuração: Verificar se a API retorna os dados corretos
            $this->messenger->addMessage('<pre>' . print_r($data, TRUE) . '</pre>', 'status');

            // Mapeia a resposta para objetos PHP
            return new ApiResponse($data);

        } catch (RequestException $exception) { // <- O erro provavelmente está aqui
            $this->showMessage(t('Failed to complete task: %error', ['%error' => $exception->getMessage()]), 'error');
            Drupal::logger('fts_api')->error('Failed to complete task: %error', ['%error' => $exception->getMessage()]);
            return FALSE;
        }
    }

    /**
     * Build options for the HTTP client.
     *
     * @param array $query
     *   The query string parameters.
     * @param string $body
     *   The request body.
     *
     * @return array
     *   The options for the HTTP request.
     */
    private function buildOptions($query, $body)
    {
        $options = [];

        // Adiciona o cabeçalho Content-Type para JSON
        $options['headers'] = [
            'Content-Type' => 'application/json',
        ];

        // Autenticação
        $options['auth'] = $this->auth();

        // Verifica se o body está no formato correto e converte para JSON, se necessário
        if ($body) {
            if (is_array($body)) {
                // Converte o array PHP para uma string JSON
                $options['body'] = json_encode($body);
            } else {
                $options['body'] = $body;
            }
        }

        // Define os parâmetros de consulta, se existirem
        if ($query) {
            $options['query'] = $query;
        }

        return $options;
    }

    /**
     * Authentication handler.
     *
     * @return array
     *   Authentication credentials.
     */
    private function auth()
    {
        return [$this->token, $this->secret];
    }

    /**
     * Show a message using the messenger service.
     *
     * @param string $message
     *   The message to display.
     * @param string $type
     *   The message type (status, error, warning).
     */
    private function showMessage($message, $type = 'status')
    {
        $this->messenger->addMessage($message, $type);
    }

    /**
     * Throttle the response.
     *
     * @param array $headers
     *   The response headers.
     *
     * @return bool|void
     *   TRUE if allowed, or sleep if throttling is needed.
     */
    private function throttle($headers)
    {
        print_r($headers['HR-Request-Rate-Count'][0]);
        if ($headers['HR-Request-Rate-Count'][0] > 99) {
            return sleep(60); // Throttle if more than 99 requests
        }
        return TRUE;
    }
}