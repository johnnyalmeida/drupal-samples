<?php

namespace Drupal\mmda_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a custom next and previous link node Block
 *
 * @Block(
 *   id = "mysite_next_prev",
 *   admin_label = @Translation("Myset Next and Prev Links"),
 * )
 */
class MysiteNextPrevBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    // Get the current node.
    $node = \Drupal::request()->attributes->get('node');

    // Get next project.
    $next = $this->get_next($node->id());
    $prev = $this->get_prev($node->id());

    // Set template variables.
    $links = array (
      'next' => $next,
      'prev' => $prev,
      );

    // Return the block markup.
    return array(
      '#theme' => 'mysite_nextprev',
      '#mysite_links' => $links,
      '#cache' => array('max-age' => 0)
    );
  }


  protected function get_next ($current_nid) {
    // Query the next node.
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'cases')
      ->condition('nid', $current_nid, '>')
      ->sort('nid', 'ASC')
      ->range(0, 1);
    
    $nids = $query->execute();

    // If the current node is the first one, get the last node.
    if(empty($nids)){
      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'cases')
        ->sort('nid', 'ASC')
        ->range(0, 1);
      
      $nids = $query->execute();
    }

    // Get the clear nid to load the node.
    foreach($nids as $n ){
      $nid = $n;
    }

    // Prepare the next project link.
    $node = \Drupal\node\Entity\Node::load($nid);
    $next = Link::createFromRoute($node->title->value, 'entity.node.canonical', array('node' => $nid))->toString();
    $url = Url::fromUri('internal:/node/' . $nid);

    $next = array(
      'url' => $url,
      'title' => $node->title->value
      );

    return $next;
  }



  protected function get_prev ($current_nid) {

    // Query the previous node.
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'cases')
      ->condition('nid', $current_nid, '<')
      ->sort('nid', 'DESC')
      ->range(0, 1);
    
    $nids = $query->execute();

    // If the current node is the last one, get the first node.
    if(empty($nids)){
      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'cases')
        ->sort('nid', 'DESC')
        ->range(0, 1);
      
      $nids = $query->execute();
    }

    // Get the clear nid to load the node.
    foreach($nids as $n ){
      $nid = $n;
    }

    // Prepare the previous project link.
    $node = \Drupal\node\Entity\Node::load($nid);
    $prev = Link::createFromRoute($node->title->value, 'entity.node.canonical', array('node' => $nid))->toString();
    $url = Url::fromUri('internal:/node/' . $nid);

    $prev = array(
      'url' => $url,
      'title' => $node->title->value
      );

    return $prev;
  }


}
