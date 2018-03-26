<?php

namespace Drupal\foobar\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin add prefix to the destination.
 *
 * @MigrateProcessPlugin(
 *   id = "foobar_prefix"
 * )
 */
class Prefix extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $prefix = $this->configuration['prefix'];

    return $prefix . $value;
  }

}
