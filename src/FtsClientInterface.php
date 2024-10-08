<?php

namespace Drupal\fts;
interface FtsClientInterface
{
    /**
     *
     * @param string $method
     *   get, post, patch, delete, etc. See Guzzle documentation.
     * @param string $endpoint
     *   The HR API endpoint
     * @param array $query
     *   Query string parameters the endpoint allows (ex. ['per_page' => 50]
     * @param array $body (converted to JSON)
     *   Utilized for some endpoints
     * @return object
     *   \GuzzleHttp\Psr7\Response body
     */
    public function connect($method, $endpoint, $query, $body);
}