---
layout: post
title:  "Миграция, простой process плагин"
date:   2018-03-26 17:01:00 +0300
tags: [migrate, drupal8]
---

В прошлой статье мы рассмотрели миграцию 
пользователей с сохранением паролей. Однако 
строку пароля нам пришлось изменить перед тем, 
как она была использоана для миграции. Сделано 
это было в методе prepareRow источника миграции.
Но это не единственный способ преобразования данных,
порой правильнее организовать код, используя process плагины.

Конфигурация импорта.

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
  pass:
    plugin: foobar_prefix
    source: pass
    prefix: 'F$'
  status:
    plugin: default_value
    default_value: 1
destination:
  plugin: entity:user
migration_dependencies: {}
```

#### Prefix.php
```php
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
```

[Файлы](https://github.com/Punk-UnDeaD/Punk-UnDeaD.github.io/tree/master/_files/2018-03-26-Migrate without fear)