<?php

namespace Drupal\fts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\fts\Client\FtsClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MyController.
 *
 * @package Drupal\my_custom_module\Controller
 */
class FtsController extends ControllerBase
{

    /**
     * Drupal\fts\Client\FtsClient definition.
     *
     * @var FtsClient
     */
    protected $ftsApiClient;

    /**
     * {@inheritdoc}
     */
    public function __construct(FtsClient $fts_api_client)
    {
        $this->ftsApiClient = $fts_api_client;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('fts.client')
        );
    }

    /**
     * Content.
     *
     * @return array
     *   Return array.
     */
    public function content()
    {
        // This would get 50 people from Planning Center on page load.
        $query = [
            'per_page' => 10
        ];
        $request = $this->ftsApiClient->connect('get', '/api/v1.0/global_clusters/', $query, []);
        $results = json_decode($request, true);

        // Renderizando os dados diretamente, sem necessidade de envolver em um array 'clusters'
        return [
            '#theme' => 'fts_list', // assign the theme [products-list.html.twig]
            '#title' => 'Rest API Client', // assign the page title
            '#pagehtml' => 'List of Data is coming from : HR.info Clusters ', // assign the string message like this
            '#data' => $results, // Passando os resultados diretamente
        ];
    }

    public function postMleva()
    {
        $endpoint = '/mleva';
        $payload = [
            'products' => [
                ['gtin' => '7896075300205', 'price' => '36.89'],
                ['gtin' => '7898955352168', 'price' => '22.99'],
                ['gtin' => '7896075300793', 'price' => '30.99']
            ],
            'company' => [
                'companyName' => 'Varejão',
                'localization' => '-22.83512749482224, -45.2265350010767'
            ],
            'user' => [
                'userName' => 'lobsom'
            ]
        ];

        // Fazendo a requisição
        $response = $this->ftsApiClient->connect('post', $endpoint, [], $payload);

        if ($response) {
            return [
                '#theme' => 'fts_list',
                '#products' => $response->products ?? [],  // Certifica-se de que os produtos sejam passados
                '#company' => $response->company ?? null,
                '#user' => $response->user ?? null,
                '#dateTime' => $response->dateTime ?? null,
            ];
        }

        return [
            '#markup' => 'Erro ao tentar recuperar dados.'
        ];
    }
}
