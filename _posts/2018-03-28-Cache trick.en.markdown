---
layout: post
title:  "#cache in template"
date:   2018-03-28 14:00:00 +0300
tags: [twig, drupal8]
---

When we actively use templates for entities
with references to other entities, it is important not to forget
set correct caching so that all changes are applied immediately.
You can do it directly in the template.

```twig
{% raw %}{{ node.uid.entity.label() }}
{{ {'#cache':{tags: node.uid.entity.getCacheTags()}} }}{% endraw %}
```