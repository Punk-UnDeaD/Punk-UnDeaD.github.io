<?php

namespace Drupal\foobar\Plugin\migrate\source;


use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Extract users from any database.
 *
 * @MigrateSource(
 *   id = "foobar_user"
 * )
 */
class User extends SqlBase {

  /**
   * Returns available fields on the source.
   *
   * @return array
   *   Available fields in the source, keys are the field machine names as used
   *   in field mappings, values are descriptions.
   */
  public function fields() {
    return [
      'name',
      'pass',
      'mail',
      'uid'
    ];
  }

  /**
   * Get the source ids.
   *
   * @return array
   *   The source ids.
   */
  public function getIds() {
    return ['uid' => ['type' => 'integer']];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $pass = $row->getSourceProperty('pass');
    $row->setSourceProperty('pass', 'F$' . $pass);
    return parent::prepareRow($row);
  }

  /**
   * @return \Drupal\Core\Database\Query\SelectInterface
   */
  public function query() {
    return $this->select('users', 'u')
      ->fields('u', $this->fields());
  }
}
