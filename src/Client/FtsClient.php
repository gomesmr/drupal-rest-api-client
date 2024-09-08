<?php

namespace Drupal\fts\Client;

use Drupal\fts\FtsClientInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Messenger\MessengerInterface;

class FtsClient implements FtsClientInterface {

  /**
   * An http client.
   *
   * @var \GuzzleHttp\ClientInterface
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
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructor.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service for displaying messages.
   */
  public function __construct(ClientInterface $http_client, MessengerInterface $messenger) {
    $this->httpClient = $http_client;
    $this->messenger = $messenger; // Now correctly injecting the messenger service
    $this->base_uri = 'http://host.docker.internal:8081'; // Update to your actual service URL
    $this->token = 'seu-token-aqui'; // Define your token
    $this->secret = 'seu-segredo-aqui'; // Define your secret
  }

  /**
   * Show a message using the messenger service.
   *
   * @param string $message
   *   The message to display.
   * @param string $type
   *   The message type (status, error, warning).
   */
  private function showMessage($message, $type = 'status') {
    $this->messenger->addMessage($message, $type);
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
  public function connect($method, $endpoint, $query, $body) {
    try {
      $response = $this->httpClient->{$method}(
        $this->base_uri . $endpoint,
        $this->buildOptions($query, json_encode($body)) // Convert body to JSON
      );
    }
    catch (RequestException $exception) {
      // Show error message using messenger service
      $this->showMessage(t('Failed to complete task: %error', ['%error' => $exception->getMessage()]), 'error');
      \Drupal::logger('fts_api')->error('Failed to complete task: %error', ['%error' => $exception->getMessage()]);
      return FALSE;
    }
    return $response->getBody()->getContents();
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
  private function buildOptions($query, $body) {
    $options = [];
      // Adiciona o cabeçalho Content-Type para JSON
  $options['headers'] = [
    'Content-Type' => 'application/json',
  ];
    $options['auth'] = $this->auth(); // Authentication
    if ($body) {
      $options['body'] = $body; // Set request body
    }
    if ($query) {
      $options['query'] = $query; // Set query parameters
    }
    return $options;
  }

  /**
   * Authentication handler.
   *
   * @return array
   *   Authentication credentials.
   */
  private function auth() {
    return [$this->token, $this->secret];
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
  private function throttle($headers) {
    print_r($headers['HR-Request-Rate-Count'][0]);
    if ($headers['HR-Request-Rate-Count'][0] > 99) {
      return sleep(60); // Throttle if more than 99 requests
    }
    return TRUE;
  }
}