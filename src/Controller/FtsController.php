<?php

namespace Drupal\fts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\fts\Client\FtsClient;
use Drupal\fts\Model\ProductGetId;
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
                '#theme' => 'fts_compare_response',
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

    /**
     * Product by Id.
     *
     * @param string $gtin
     *   The GTIN of the product.
     *
     * @return array
     *   The product data or an error message.
     */
    public function getProductByGtin($gtin)
    {
        $endpoint = '/mleva/product/';
        $query = ['gtin' => $gtin];

        try {
            $response = $this->ftsApiClient->connect('get', $endpoint, $query, null);

            if ($response) {
                $product = new ProductGetId($response);

                return [
                    '#theme' => 'fts_get_product_by_id',
                    '#product' => $product,
                ];
            }

            return ['#markup' => 'Produto não encontrado.'];

        } catch (\Exception $exception) {
            $this->messenger()->addError(t('Erro ao buscar o produto: @error', ['@error' => $exception->getMessage()]));
            \Drupal::logger('fts_api')->error('Erro ao buscar o produto: %error', ['%error' => $exception->getMessage()]);
            return ['#markup' => 'Erro ao buscar o produto.'];
        }

    }
}

