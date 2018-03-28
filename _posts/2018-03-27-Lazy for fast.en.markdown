---
layout: post
title:  "JS personalization, performance and lazybuild"
date:   2018-03-27 14:00:00 +0300
tags: [render, drupal8]
---

We often need create a personal block
or make available information about the user for the JavaScript.
Even if only the user name, but it immediately breaks us
the entire caching system.
Or a block with activity statistic.
A bubble cache will create a more specific context,
will reduce the caching time.
The "Dynamic Page Cache" module just does not work,
if #cache\[contexts] contains 'users'
And more advanced techniques will have to be forgotten.

To ensure that the caching of large fragments is not destroyed by small
you can use the lazybuild trick.

To use it in a large fragment, you first insert
special item containing
only information about the need for later processing.

Then, before the data is returned to the client, 
all these elements will be processed
and then the page will be formed.

Also this mechanism places scripts, styles
and object drupalSettings.

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

This code allows us to put up-to-date information
about the user in the drupalSettings object on all pages
except for administrative and not to violate the caching. 