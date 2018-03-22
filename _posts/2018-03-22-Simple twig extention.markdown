---
layout: post
title:  "Создание простого расширения Twig"
date:   2018-03-22 18:58:00 +0300
tags: [twig, drupal8]
---
Очень часто случается, что наличной в Twig логики не хватает и есть необходимость быстро и просто расширить имеющийся вывод новыми функциями и фильтрами.
Сделать это не просто, а очень просто.

Создадим модуль и описание сервиса к нему.

##### foobar.info.yml
```
name: FooBar
description: FooBar functionality
package: FooBar
type: module
core: 8.x
version: 8.x-1.0
```

##### foobar.services.yml
```
services:
  foobar.twig.fizzbuzz_extension:
    class: Drupal\foobar\Twig\Extension\FizzBuzz
    tags:
      - { name: twig.extension }
```
Последние две строки как раз и гарантируют нам, что FizzBuzz будет поключен к Twig в качестве расширения автоматически.

