<?php

namespace Drupal\pixelthis\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for pixelthis routes.
 */
class TagsParserController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  /**
   * Undocumented function.
   *
   * @return array
   */
  private function getTagsExposedViewsForm() : array {

    $form = [];
    $view_id = 'saa';
    $display_id = 'page_3';

    $view = Views::getView($view_id);

    if ($view) {
      $view->setDisplay($display_id);
      $view->initHandlers();
      $form_state = (new FormState())
        ->setStorage([
          'view' => $view,
          'display' => &$view->display_handler->display,
          'rerender' => TRUE,
        ])
        ->setMethod('get')
        ->disableRedirect()
        ->setAlwaysProcess();
      $form_state->set('rerender', NULL);
      $form = \Drupal::formBuilder()->buildForm('\Drupal\views\Form\ViewsExposedForm', $form_state);
      $form['#attributes']['class'][] = 'wpi-experts-form';
      $form['field_point_person_target_id']['#access'] = FALSE;
      $form['field_bio_value_wrapper']['#type'] = 'markup';
      $form['field_bio_value_wrapper']['#title_display'] = 'invisible';
      $form['field_bio_value_wrapper']['field_bio_value_op']['#access'] = FALSE;
      $form['actions']['submit']['#value'] = $this->t('Search');
    }

    return $form;
  }

}
