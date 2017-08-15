Revision history
=================================

Notes for development v1.1.0*
---------------------------------

* Rename add to any when adding routes without request method?
* Rename RouterInjectable to Router.
* Mark RouterInjectable as obsolete and implement it as extending Router.
* Review the test suite.
* Add route length (max, min) as a feature like type.
* Add (MV)C as route and dispatch to controller, with before and after methods.
* Add forward to enable forwarding to another route handler, like MVC triads.
* (Use regexp to match route).


v1.0.12 (2017-08-15)
---------------------------------

* Removing getName() and replacing with getInfo().
* Adding member info to the Route.
* Adding module anax/di as required in composer.json.
* Loading routes from configuration file.


v1.0.11 (2017-08-10)
---------------------------------

* Adding class Router as a DI enabled version.
* Add getName() for Route.


v1.0.10 (2017-08-10)
---------------------------------

* Add comment in route file to make 404 last in sequence.


v1.0.9 (2017-08-03)
---------------------------------

* Adding config/ with some default routes.


v1.0.8 (2017-06-27)
---------------------------------

* Fix unittest passing.
* Fix Route::checkPartAsArgument missing type vvariable.


v1.0.7 (2017-06-27)
---------------------------------

* Add Route::getRequestMethod() to show information on request method for route.
* Load routes from configuration file.
* Made RouterInjectable injectable with $app.


v1.0.6 (2017-06-27)
---------------------------------

* Modify type of integer argument when validatet using digit.


v1.0.5 (2017-04-24)
---------------------------------

* Adding documentation and testcases for documentation.
* Adding method RouterInjectable::always() as a default routehandler matching any route and request method.
* Rearrange methods to improve readability.
* Add docblocks for properties.
* Add support for adding several path rules with one route->add().


v1.0.4 (2017-04-13)
---------------------------------

* Add support for path/** to match subpaths.
* Fix composer validate PHP version in require-dev. 


v1.0.3 (2017-03-26)
---------------------------------

* Extending support for default routes to partly include "\*\*" and null, matching any route. 
* Support adding request method as string separated by |


v1.0.2 (2017-03-26)
---------------------------------

* Allow matching of several routehandlers having the same path.
* Add testcases.


v1.0.1 (2017-03-13)
---------------------------------

* Add arguments as part of route.
* Arguments can be validated as alpha, alphanum, digit, hex.
* Support different routes per request methods.


v1.0.0 (2017-03-07)
---------------------------------

* Making standalone without `$di`.
* Enhancing unittest.
* Adding exceptions.
* Cleanup makefile.
* Extracted from anax to be its own module.
