---
layout: post
title:  "JS персонализация, производительность и lazybuild"
date:   2018-03-27 14:00:00 +0300
tags: [render, drupal8]
---

Мы нередко сталкиваемся с задачей сделать персональный блок
или сделать доступной информацию о пользователе для яваскрипта. 
Пусть даже только имя пользователя, но оно сразу ломает нам
всю систему кеширования.
Или блок со статистикой активности. 
Всплывание кеша создаст более специфичный контекст, 
уменьшит время кеширования.
Модуль "Dynamic Page Cache" просто не срабатывает, 
если #cache\[contexts] содержит 'users'
А о более продвинутых техниках придётся забыть.

Чтоб кеширование больших фрагментов не разрушалось маленькими 
и применяется техника lazybuild.
Для её использовании в большой фрагмент сначала вставляется 
специальный элемент содержащий
только информацию о необходимости более поздней обработки.
Затем перед самой отдачей данных криенту все эти этементы 
обсчитываются и тогда страница будет сформирована.
Заодно этим механизмом в разметку помещаются скрипты, стили 
и объект drupalSettings.

```PHP
   function foobar_preprocess_html(&$html) {
    $route = \Drupal::routeMatch()->getRouteObject();
    $is_admin = \Drupal::service('router.admin_context')
        ->isAdminRoute($route) && \Drupal::currentUser()
        ->hasPermission('view the administration theme');
    if ($is_admin) {
      return;
    }

    $html['page_top']['user_info'] =  [
      '#lazy_builder'       => ['foobar_userinfo', []],
      '#create_placeholder' => TRUE,
    ];

  }

  function foobar_userinfo() {
    $user = \Drupal::currentUser();
    $user_info = [
      'uid'             => $user->id(),
      'name'            => $user->getAccountName(),
      'isAnonymous'     => $user->isAnonymous(),
      'isAuthenticated' => $user->isAuthenticated(),
      'csrf_token'      => \Drupal::csrfToken()->get(),
    ];

    return [
      [
        '#attached'   => [
          'drupalSettings' => ['user' => $user_info],
        ],
      ],
    ];
  }
```

Этот код позволяет нам поместить актуальную информацию
о пользователе в объект drupalSettings на всех страницах
кроме административной и не нарушить при этом кеширование.  