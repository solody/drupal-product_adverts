<?php

namespace Drupal\product_adverts\Plugin\Block;

use Drupal\commerce\CommerceContentEntityStorage;
use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\product_adverts\Entity\ProductAdverts;

/**
 * Provides a 'AdvertsLatestProducts' block.
 *
 * @Block(
 *  id = "adverts_latest_products",
 *  admin_label = @Translation("Adverts latest products"),
 * )
 */
class AdvertsLatestProducts extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'quantity' => 10,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Retrieve existing configuration for this block.
    $config = $this->getConfiguration();

    /** @var ProductAdverts $product_advert */
    $quantity = isset($config['quantity']) ? $config['quantity'] : 10;

    $build = [];
    $build['#theme'] = 'adverts_latest_products';

    // Finds the latest products.
    /** @var CommerceContentEntityStorage $productStorage */
    $productStorage = \Drupal::entityTypeManager()->getStorage('commerce_product');
    $product_ids = $productStorage->getQuery()
      ->sort('created', 'DESC')
      ->range(0, $quantity)
      ->execute();

    $productEntities = Product::loadMultiple($product_ids);
    $products = [];
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('commerce_product');
    foreach ($productEntities as $product) {
      $products[] = [
        'content' => $view_builder->view($product, 'teaser'),
        'entity' => $product
      ];
    }

    $build['#products'] = $products;

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Retrieve existing configuration for this block.
    $config = $this->getConfiguration();

    /** @var ProductAdverts $product_advert */
    $quantity = isset($config['quantity']) ? $config['quantity'] : 10;

    $form['quantity'] = array(
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
      '#default_value' => $quantity
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('quantity', $form_state->getValue('quantity'));
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    if ((int)$form_state->getValue('quantity') < 1) {
      $form_state->setErrorByName('quantity', t('Quantity can not be less than 1.'));
    }
  }

  public function getCacheMaxAge() {
    // If you want to disable caching for this block.
    return 0;
  }
}
