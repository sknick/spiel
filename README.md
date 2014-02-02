Spiel
=====

Spiel is a small and lightweight PHP framework for creating RPC-like web
services that can provide interface documentation "on the fly" to requesting
clients. The current version supports returning JSON-encoded objects. A
precursor to this framework has proven itself to be very useful in various
production environments for a few years now.

Expect more documentation in the future, but for now, take a look at the
"Hello World!" example in the examples directory. To run it, put the contents of
the src directory somewhere in your PHP include path and then put the
sayHello.php file into your web server's publishing path. When you request the
sayHello.php file, you will be provided with documentation on its interface. By
requesting the file again but appending an "x" request parameter (e.g.,
"http://your.server/sayHello.php?x"), the web service will be invoked and you
will be returned a JSON-encoded object indicating the service response.

Spiel requires PHP 5.3 or higher and has been tested in an Apache 2 / PHP
environment.
