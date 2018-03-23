---
layout: post
title:  "Migrating users with a password without fear"
date:   2018-03-23 17:31:00 +0300
tags: [migrate, drupal8]
---

From time to time you have to migrate
users along with passwords.
Of course, you can always reset them and users
can always create them anew,
but why not try to save this data.

```yaml
name: FooBar
description: FooBar functionality
package: FooBar
type: module
core: 8.x
version: 8.x-1.0

dependencies:
  - migrate
  - migrate_plus
```

The source of our users is the table
'user' in database 'migrate'. For that in settings.php
you must add a new connection.

#### settings.php
```php
$databases['migrate']['default'] = array (
  'database' => 'database',
  'username' => 'username',
  'password' => 'password',
  'prefix' => '',
  'host' => '127.0.0.1',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);
```

Now configure the plugin. As easy as pie.

#### migrate_plus.migration.foobar_user.yml
```yaml
id: foobar_user
label: Migrate user
source:
  plugin: foobar_user
  target: migrate
process:
  name: name
  mail: mail
  pass: pass
  status:
    plugin: default_value
    default_value: 1
destination:
  plugin: entity:user
migration_dependencies: {}
```

But to write a plug-in you will need a little more code.

#### User.php
```php
  public function fields() {
    return [
      'name',
      'pass',
      'mail',
      'uid'
    ];
  }

  public function getIds() {
    return ['uid' => ['type' => 'integer']];
  }

  public function prepareRow(Row $row) {
    $pass = $row->getSourceProperty('pass');
    $row->setSourceProperty('pass', 'F$' . $pass);
    return parent::prepareRow($row);
  }

  public function query() {
    return $this->select('users', 'u')
      ->fields('u', $this->fields());
  }
```

Nothing complicated, but in order to separate
migrated password, the prefix F$ is added to it.

Turn on our module and run the migration.
```bash
drush en foobar
drush mim foobar_user
```
You can make sure that new
users and their passwords correspond to the old ones with our prefix.
However, logging in with the old password will fail us, because
authentication system Drupal does not know anything about this.
To fix it, we will create our password verification service
instead of the existing one.

#### services.yml
```yaml
services:
  password:
    class: Drupal\foobar\Password\FoobarHashedPassword
    arguments: [16]
```
#### FoobarHashedPassword.php
```php
  protected function goodOldHash($password) {
    return md5($password);
  }

  public function check($password, $hash) {
    if (substr($hash, 0, 2) == 'F$') {
      return 'F$' . $this->goodOldHash($password) === $hash;
    }
    return parent::check($password, $hash);
  }
}
```
[Files](https://github.com/Punk-UnDeaD/Punk-UnDeaD.github.io/tree/master/_files/2018-03-23-Migrate without fear)