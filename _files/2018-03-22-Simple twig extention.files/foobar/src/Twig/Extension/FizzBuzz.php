<?php

namespace Drupal\foobar\Twig\Extension;

class FizzBuzz extends \Twig_Extension {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'foobar_fizzbuzz';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('fizzbuzz', [$this, 'fizzBuzz'])
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new \Twig_SimpleFilter('fizzbuzz', [$this, 'fizzBuzz']),
    ];
  }

  /**
   * @param int $i
   *
   * @return string
   */
  public function fizzBuzz($i) {
    $s = '';
    if ($i % 3 === 0) {
      $s .= 'Fizz';
    }
    if ($i % 5 === 0) {
      $s .= 'Buzz';
    }
    if ($i % 3 && $i % 5) {
      $s .= $i;
    }
    return $s;
  }
}
