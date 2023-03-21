<?php

namespace Drupal\product_adverts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\product_adverts\Entity\ProductAdverts;

/**
 * Provides a 'AdvertsBanner' block.
 *
 * @Block(
 *  id = "adverts_banner",
 *  admin_label = @Translation("Adverts banner"),
 * )
 */
class AdvertsBanner extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'product_advert' => '',
      'show_new_tag' => TRUE,
      'text_color' => '#000000',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Retrieve existing configuration for this block.
    $config = $this->getConfiguration();

    /** @var ProductAdverts $product_advert */
    $advert = isset($config['product_advert']) ? ProductAdverts::load($config['product_advert']) : null;

    /** @var \Drupal\commerce_price\CurrencyFormatter $CurrencyFormatter */
    $CurrencyFormatter = \Drupal::service('commerce_price.currency_formatter');

    $build = [];

    if ($advert instanceof ProductAdverts) {
      $build['#theme'] = 'adverts_banner';
      $build['#show_new_tag'] = isset($config['show_new_tag']) ? $config['show_new_tag'] : false;
      $build['#text_color'] = isset($config['text_color']) ? $config['text_color'] : '#000000';
      $build['#advert'] = [
        'title' => $advert->getTitle(),
        'sub_title' => $advert->getSubTitle(),
        'summary' => $advert->getSummary(),
        'image' => $advert->get('image')->entity,
        'product' => $advert->getProduct(),
        'url_to_product' => Url::fromRoute('entity.commerce_product.canonical', ['commerce_product' => $advert->getProduct()->id()]),
        'price_of_product' => $CurrencyFormatter->format(
          $advert->getProduct()->getDefaultVariation()->getPrice()->getNumber(),
          $advert->getProduct()->getDefaultVariation()->getPrice()->getCurrencyCode(),
          [
            'minimum_fraction_digits' => '0',
            'maximum_fraction_digits' => '0',
            'style' => 'accounting'
          ])
      ];
    }

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
    $product_advert = isset($config['product_advert']) ? ProductAdverts::load($config['product_advert']) : null;

    // Add a form field to the existing block configuration form.
    $form['product_advert'] = array(
      '#type' => 'entity_autocomplete',
      '#title' => t('Product advert'),
      '#target_type' => 'product_adverts',
      '#default_value' => $product_advert,
    );

    $form['show_new_tag'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Show new tag'),
      '#default_value' => isset($config['show_new_tag']) ? $config['show_new_tag'] : false
    );

    $form['text_color'] = array(
      '#type' => 'color',
      '#title' => $this->t('Text color'),
      '#default_value' => isset($config['text_color']) ? $config['text_color'] : '#000000'
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('product_advert', $form_state->getValue('product_advert'));
    $this->setConfigurationValue('show_new_tag', $form_state->getValue('show_new_tag'));
    $this->setConfigurationValue('text_color', $form_state->getValue('text_color'));
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    $product_advert = ProductAdverts::load($form_state->getValue('product_advert'));
    if (!$product_advert instanceof ProductAdverts) {
      $form_state->setErrorByName('product_advert', t('Needs select an product_advert entity.'));
    }
  }

  public function getCacheMaxAge() {
    // If you want to disable caching for this block.
    return 0;
  }
}
