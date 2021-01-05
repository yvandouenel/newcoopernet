<?php

namespace Drupal\memo\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
//use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\rest\ResourceResponse;

/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "pronunciation",
 *   label = @Translation("Cartes - Pronunciation"),
 *   uri_paths = {
 *     "canonical" = "/memo/pronunciation/{word}"
 *   }
 * )
 */
class WordPronunciation extends ResourceBase {
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get($word = "") {
    $key = "k5HRNv5msL";
    $url = "http://voicecup.com/api?" .
      "q=" . $word . "&" .
      "key=" . $key .
      "&l=en&from=1&size=15&length_min=5&length_max=50&duration_min=1&duration_max=25&format=jsonp";
    $data = file_get_contents($url);
    
    return new ResourceResponse($data);
    //return new JsonResponse($data);
  }
}
