---
layout: post
title:  "Создание простого расширения Twig"
date:   2018-03-22 18:58:00 +0300
tags: [twig, drupal8]
---
Очень часто случается, что наличной в Twig логики не хватает и есть необходимость быстро и просто расширить имеющийся вывод новыми функциями и фильтрами.
Сделать это не просто, а очень просто.

Создадим модуль и описание сервиса к нему.

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
Последние две строки как раз и гарантируют нам, что FizzBuzz будет поключен к Twig в качестве расширения автоматически.

#### Код FizzBuzz.php

Метод getName класса FizzBuzz должен возвращать уникальное имя. 
```php
  public function getName() {
    return 'foobar_fizzbuzz';
  }
```
Методы getFunctions и getFilters соответственно определяют новую функцию и фильтр.
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
С именем fizzbuzz и исполняемым кодом в \[$this, 'fizzBuzz'].

Сам метод fizzBuzz мог бы выглядеть вот так.
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

#### Теперь вы везде сможете написать

```twig
{% raw %}{% for i in 1..100 %}
  {{ i }} - {{ fizzbuzz(i) }} - {{ i|fizzbuzz }}
  <hr>
{% endfor %}{% endraw %}
```

[Файлы](https://github.com/Punk-UnDeaD/Punk-UnDeaD.github.io/tree/master/_files/2018-03-22-Simple%20twig%20extention.files)