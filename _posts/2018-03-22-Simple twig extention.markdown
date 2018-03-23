---
layout: post
title:  "Creating a simple extension Twig"
date:   2018-03-23 12:58:00 +0300
tags: [twig, drupal8]
---
It often happens that Twig does not have enough logic, and you need to quickly and easily extend the available output with new functions and filters.
It's very simple.


Create a module and a description of the service to it.

#### foobar.info.yml
```yaml
name: FooBar
description: FooBar functionality
package: FooBar
type: module
core: 8.x
version: 8.x-1.0
```

#### foobar.services.yml
```yaml
services:
  foobar.twig.fizzbuzz_extension:
    class: Drupal\foobar\Twig\Extension\FizzBuzz
    tags:
      - { name: twig.extension }
```

The last two lines just guarantee that FizzBuzz will be connected to Twig as an extension automatically.

#### FizzBuzz.php

getName method of FizzBuzz class must return unique value.
```php
  public function getName() {
    return 'foobar_fizzbuzz';
  }
```
getFunctions and getFilters methods respectively define a new function and filter.
```php
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('fizzbuzz', [$this, 'fizzBuzz'])
    ];
  }

  public function getFilters() {
    return [
      new \Twig_SimpleFilter('fizzbuzz', [$this, 'fizzBuzz']),
    ];
  }
```
with name fizzbuzz and callable \[$this, 'fizzBuzz'].

fizzBuzz method look like next code
```php
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
```

####Now you can write
```twig
{% raw %}{% for i in 1..100 %}
  {{ i }} - {{ fizzbuzz(i) }} - {{ i|fizzbuzz }}
  <hr>
{% endfor %}{% endraw %}
```

[Файлы](https://github.com/Punk-UnDeaD/Punk-UnDeaD.github.io/tree/master/_files/2018-03-22-Simple%20twig%20extention.files)