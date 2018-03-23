<?php

namespace Drupal\foobar\Password;


use Drupal\Core\Password\PhpassHashedPassword;

class FoobarHashedPassword extends PhpassHashedPassword {

  /**
   * @param $password
   *
   * @return string
   */
  protected function goodOldHash($password) {
    return md5($password);
  }

  /**
   * @param string $password
   * @param string $hash
   *
   * @return bool
   */
  public function check($password, $hash) {
    if (substr($hash, 0, 2) == 'F$') {
      return 'F$' . $this->goodOldHash($password) === $hash;
    }
    return parent::check($password, $hash);
  }
}
