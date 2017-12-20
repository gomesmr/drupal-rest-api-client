<?php
namespace Drupal\fts\Client;

use Drupal\fts\FtsClientInterface;
use \GuzzleHttp\ClientInterface;
use \GuzzleHttp\Exception\RequestException;

class FtsClient implements FtsClientInterface {
  /**
   * An http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;
  /**
   * Hr Base URI.
   *
   * @var string
   */
  protected $base_uri;
  /**
   * Constructor.
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
    $this->base_uri = 'https://www.humanitarianresponse.info';
  }
  /**
   * { @inheritdoc }
   */
  public function connect($method, $endpoint, $query, $body) {
    try {
      $response = $this->httpClient->{$method}(
        $this->base_uri . $endpoint,
        $this->buildOptions($query, $body)
      );
    }
    catch (RequestException $exception) {
      drupal_set_message(t('Failed to complete Hr Task "%error"', ['%error' => $exception->getMessage()]), 'error');
      \Drupal::logger('fts_api')->error('Failed to complete Hr Task "%error"', ['%error' => $exception->getMessage()]);
      return FALSE;
    }
    $headers = $response->getHeaders();
    $this->throttle($headers);
    return $response->getBody()->getContents();
  }
  /**
   * Build options for the client.
   */
  private function buildOptions($query, $body) {
    $options = [];
    $options['auth'] = $this->auth();
    if ($body) {
      $options['body'] = $body;
    }
    if ($query) {
      $options['query'] = $query;
    }
    return $options;
  }
  /**
   * Throttle response.
   *
   * 100 per 60s allowed.
   */
  private function throttle($headers) {
    print_r($headers['HR-Request-Rate-Count'][0]);
    if ($headers['HR-Request-Rate-Count'][0] > 99) {
      return sleep(60);
    }
    return TRUE;
  }
  /**
   * Handle authentication.
   */
  private function auth() {
    return [$this->token, $this->secret];
  }
}