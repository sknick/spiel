Spiel
=====

Spiel is a small and lightweight object-oriented PHP framework for creating
RPC-like web services that can provide interface documentation "on the fly" to
requesting clients. The current version supports returning JSON-encoded objects.
A precursor to this framework has proven itself to be very useful in various
production environments for a few years now.

Spiel requires PHP 5.3 or higher and has been tested in an Apache 2 / PHP
environment.

API documentation is available in the docs/html folder (open index.htm). In
addition, there are some examples of using the framework in the examples folder:

- HelloWorld: Very simple example implementing the ubiquitous "Hello World"
  functionality.
- EmployeeManager: More full-fledged example of implementing web services using
  the framework. The web services provide an interface for managing employee
  information stored in an in-memory database on the server. While the web
  services shown are insufficient to completely provide an interface to managing
  employee information (for example, the complete set of "CRUD" functions are
  not available), they are sufficient for illustrating most of the features of
  the Spiel framework.

To see the examples in action, put a folder called `lib` somewhere in your PHP
include path; this folder should contain the contents of the `src` directory of
this framework. In addition, for the EmployeeManager example, also put the
contents of its `private` folder in your PHP include path. Finally, place the
contents of the `public` folder of the examples into your publishing path
somewhere. For example, in my local setup, I have the Spiel framework in
`/home/sknick/Source/spiel`, and I created the following symbolic link which
makes the Spiel framework files available to PHP source code using the path
"lib/spiel.php" (e.g., source code can specify `require_once("lib/spiel.php")`):

```
/home/sknick/Source/lib -> /home/sknick/Source/spiel/src
```

I then specified the following in my php.ini:

```
include_path = ".:/home/sknick/Source:/home/sknick/Source/spiel/examples/EmployeeManager/private"
```

Since I have my Apache HTTPD instance's DocumentRoot set to `/home/sknick/www`,
I created the following symbolic links in that directory:

```
/home/sknick/www/EmployeeManager -> /home/sknick/Source/spiel/examples/EmployeeManager/public
/home/sknick/www/HelloWorld -> /home/sknick/Source/spiel/examples/HelloWorld/public
```

With the above in place, I can now access the examples using URLs of
`http://127.0.0.1/EmployeeManager` and `http://127.0.0.1/HelloWorld`.
