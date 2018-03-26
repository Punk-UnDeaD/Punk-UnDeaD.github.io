---
layout: post
title:  "Migrate, simple process plugin"
date:   2018-03-26 17:01:00 +0300
tags: [migrate, drupal8]
---

In the previous post we looked at migration
users with saving passwords.

However we had to change the password value,
before it was used for migration.

We made by it in the method 'prepareRow' source of migration.

But this is not the only way to transform data,
sometimes it's more correct to organize the code using the process plugin.

Configuration.

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

Code

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

[Files](https://github.com/Punk-UnDeaD/Punk-UnDeaD.github.io/tree/master/_files/2018-03-26-Migrate without fear)