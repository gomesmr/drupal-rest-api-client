<?php

namespace Drupal\fts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\fts\Client\FtsClient;

/**
 * Class MyController.
 *
 * @package Drupal\my_custom_module\Controller
 */
class FtsController extends ControllerBase {

  /**
   * Drupal\fts\Client\FtsClient definition.
   *
   * @var \Drupal\fts\Client\FtsClient
   */
  protected $ftsApiClient;

  /**
   * {@inheritdoc}
   */
  public function __construct(FtsClient $fts_api_client) {
    $this->ftsApiClient = $fts_api_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
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
  public function content() {
    // This would get 50 people from Planning Center on page load.
    $query = [
      'per_page' => 10
    ];
    $request = $this->ftsApiClient->connect('get', '/api/v1.0/global_clusters/', $query, []);
    $results = json_decode($request, true);
    //return [];
    $data = array();
    
    // # add all the data in one multiple dim array
    $data['clusters'] = $results;
    
    // display the content in the middle section of the page
    return [
      '#theme' => 'fts_list', // assign the theme [products-list.html.twig]
      '#title' => 'Rest API Client', // assign the page title
      '#pagehtml' => 'List of Data is coming from : HR.info Clusters ', // assign the string message like this
      '#data' => $data
    ];

   //return $build;

  }
}