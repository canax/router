Revision history
=================================

Notes for development v1.1.0*
---------------------------------

* Rename RouterInjectable to Router.
* Mark RouterInjectable as obsolete and implement it as extending Router.


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
