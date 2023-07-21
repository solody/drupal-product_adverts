<?php

namespace Drupal\product_adverts\Normalizer;

use Drupal\product_adverts\Entity\ProductAdverts;
use Drupal\serialization\Normalizer\ContentEntityNormalizer;

/**
 * {@inheritdoc}
 */
class ProductAdvertsNormalizer extends ContentEntityNormalizer {

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL, array $context = []): bool {
    if ($data instanceof ProductAdverts) {
      $route_name = \Drupal::routeMatch()->getRouteName();
      return $route_name === 'view.api_product_adverts.rest_export_1';
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($entity, $format = NULL, array $context = []): float|array|\ArrayObject|bool|int|string|null {

    $data = parent::normalize($entity, $format, $context);
    /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
    $product = $entity->get('product_id')->entity;

    $image = NULL;
    if ($product->hasField('image')) {
      $image = file_create_url($product->get('image')->entity->getFileUri());
    }

    $data['_product'] = [
      'id' => $product->id(),
      'title' => $product->getTitle(),
      'image' => $image,
      'price' => $product->getDefaultVariation()->getPrice()->toArray(),
    ];

    $this->addCacheableDependency($context, $product);
    $this->addCacheableDependency($context, $product->getDefaultVariation());

    return $data;
  }

}
