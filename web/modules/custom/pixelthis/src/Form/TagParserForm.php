<?php

namespace Drupal\pixelthis\Form;

use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Batch Api parser for looking tags into a new pdf file.
 */
class TagParserForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tag_parser_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $nid = NULL;
    $current_path = \Drupal::service('path.current')->getPath();
    $path_parts = explode('/', $current_path);

    if ($path_parts[1] === 'node' && ctype_digit($path_parts[2])) {
      $nid = (int) $path_parts[2];
    }

    if ($nid === NULL) {
      return ['#markup' => $this->t('Something went wrong please try again!')];
    }

    $form['#attributes']['data-nid'] = $nid;

    $form['parse_tags'] = [
      '#type' => 'submit',
      '#value' => $this->t('Parse all tags'),
    ];

    $form['#attributes']['data-tags'] = implode(',', $this->getTagResults($nid));

    $form['#theme'] = 'pixelthis_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // saa?=ΦΕΚ.
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
      'vid' => 'tags',
    ]);
    $operations = [
          ['pixelthis_parse_pdf_tags', [$terms, $form['#attributes']['data-nid']]],
    ];
    $batch = [
      'title' => $this->t('Parsing All Tags ...'),
      'operations' => $operations,
      'finished' => 'pixelthis_parse_pdf_tags_finished',
    ];
    batch_set($batch);

    $form_state->setRebuild(TRUE);

  }

  /**
   *
   */
  private function getTagResults($nid) {

    $node = Node::load($nid);
    $tag_ids = [];
    if ($node->hasField('field_tags') && !$node->get('field_tags')->isEmpty()) {
      $tags = $node->get('field_tags');
      foreach ($tags as $key => $tag) {
        $tag_ids[] = $tag->target_id;
      }
    }

    return $tag_ids;

  }

}
