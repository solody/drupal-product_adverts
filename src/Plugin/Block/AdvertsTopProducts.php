<?php

namespace Drupal\product_adverts\Plugin\Block;

use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\product_adverts\Entity\ProductAdverts;
use Drupal\product_adverts\TopProductsCreatorInterface;
use Drupal\taxonomy\TermStorageInterface;

/**
 * Provides a 'AdvertsTopProducts' block.
 *
 * @Block(
 *  id = "adverts_top_products",
 *  admin_label = @Translation("Adverts top products"),
 * )
 */
class AdvertsTopProducts extends BlockBase {

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
    $config = $this->getConfiguration();

    /** @var ProductAdverts $product_advert */
    $quantity = isset($config['quantity']) ? $config['quantity'] : 10;

    /** @var TopProductsCreatorInterface $creator */
    $creator = \Drupal::service('product_adverts.top_products_creator');
    return $creator->build('all', $quantity);
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
