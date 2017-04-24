Anax Router
==================================

[![Join the chat at https://gitter.im/canax/router](https://badges.gitter.im/canax/router.svg)](https://gitter.im/canax/router?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Latest Stable Version](https://poser.pugx.org/anax/router/v/stable)](https://packagist.org/packages/anax/router)
[![Build Status](https://travis-ci.org/canax/router.svg?branch=master)](https://travis-ci.org/canax/router)
[![CircleCI](https://circleci.com/gh/canax/router.svg?style=svg)](https://circleci.com/gh/canax/router)
[![Build Status](https://scrutinizer-ci.com/g/canax/router/badges/build.png?b=master)](https://scrutinizer-ci.com/g/canax/router/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/canax/router/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/canax/router/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/canax/router/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/canax/router/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f525d26b-16a0-414e-b587-ce10728a380b/mini.png)](https://insight.sensiolabs.com/projects/f525d26b-16a0-414e-b587-ce10728a380b)

Anax Router module.

A standalone router supporting request methods, extracting arguments from path, validating arguments.

The router will try all routes, by the order they were added and execute all matching routes. Usine `exit()` will prevent futher routes from being matched.



Install
------------------

```bash
$ composer require anax/router
```



Usage
------------------

This is how you can use the router.



### Add a route with a handler

```php
$router = new \Anax\Router\RouterInjectable();

$router->add("about", function() {
    echo "about";
});

// try it out
$router->match("about");
// about
```



### Add several routes with one handler

```php
$router = new \Anax\Router\RouterInjectable();

$router->add(["info", "about"], function() {
    echo "info or about";
});

// try it out
$router->match("info");
$router->match("about");
// info or about
// info or about
```




License
------------------

This software carries a MIT license.



```
 .  
..:  Copyright (c) 2013 - 2017 Mikael Roos, mos@dbwebb.se
```
