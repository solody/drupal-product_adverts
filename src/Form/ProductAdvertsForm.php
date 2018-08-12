<?php

namespace Drupal\product_adverts\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Product adverts edit forms.
 *
 * @ingroup product_adverts
 */
class ProductAdvertsForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\product_adverts\Entity\ProductAdverts */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Product adverts.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Product adverts.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.product_adverts.canonical', ['product_adverts' => $entity->id()]);
  }

}
