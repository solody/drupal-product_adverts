<?php

namespace Drupal\product_adverts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\product_adverts\Entity\ProductAdverts;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides a 'AdvertsSwiper' block.
 *
 * @Block(
 *  id = "adverts_swiper",
 *  admin_label = @Translation("Adverts swiper"),
 * )
 */
class AdvertsSwiper extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  /**
   * Constructs a new AdvertsSwiper object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function build() {
    /** @var ProductAdverts[] $product_adverts */
    $product_adverts = ProductAdverts::loadMultiple();
    $data = [];
    foreach ($product_adverts as $advert) {
      $data[] = [
        'title' => $advert->getTitle(),
        'image' => $advert->get('image')->entity
      ];
    }
    $build = [];
    $build['#attached']['library'][] = 'product_adverts/swiper';
    $build['adverts_swiper'] = [
      '#theme' => 'adverts_swiper',
      '#adverts' => $data
    ];

    return $build;
  }

  public function getCacheMaxAge()
  {
    return 0;
  }
}
