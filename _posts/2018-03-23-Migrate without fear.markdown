---
layout: post
title:  "Миграция пользователей с сохранением пароля"
date:   2018-03-23 17:31:00 +0300
tags: [migrate, drupal8]
---

Во время миграции нередко возникает необходимость 
перенести пользователей вместе с паролями.
Конечно всегда можно сбросить их и пользователи 
всегда смогут создать их заново, 
но отчего бы не попробовать сохранить эти данные. 

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

Источником пользователей у нас выступает таблица 
users базы migrate. Для чего в settings.php
необходимо добавить новое подключение.

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

Теперь конфигурация плагина. Проще простого.

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

А вот для написания плагина понадобится чуть больше кода.

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

Ничего сложного, но для того, чтоб выделить
мигрированный пароль, к нему добавляется префикс F$.

Включим наш модуль и запустим миграцию.
```bash
drush en foobar
drush mim foobar_user
```
Можно убедиться, что в базе данных были созданы новые
пользователи и их пароли соответствуют старым с нашим префиксом.
Однако залогинится со старым паролем нам неудасться, потому что
система аутентификации Друпал ничего про это не знает.
Чтобы это исправить, мы создадим свой сервис проверки пароля
взамен существующего.

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
[Файлы](https://github.com/Punk-UnDeaD/Punk-UnDeaD.github.io/tree/master/_files/2018-03-23-Migrate without fear)