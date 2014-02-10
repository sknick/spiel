#Spiel#

Spiel is a small object-oriented PHP framework for creating RPC-like web
services that can provide interface documentation "on the fly" to requesting
clients. The current version supports returning JSON-encoded objects. A
precursor to this framework has proven itself to be very useful in various
production environments for a few years now.

###Rationale###
So you've written some PHP web services that your client-side code can call
using an Ajax approach or what-have-you. Your web services work well and are
relatively robust even if they're not "Big Web Services" or implement the
complete RESTful set of HTTP methods. The problem now is in your ability to
communicate what the interface to each web service looks like--what GET or POST
parameters you can invoke each with, and what the returned data looks like.
Sure, you can inspect the source code for a web service to determine this, or
you can simply try to invoke one to see what it returns (assuming you can guess
its parameters correctly), but this is far from ideal and is a recipe for
disaster if you're working on a team or trying to make your web services
available as an API to clients.

This is where Spiel comes in. By implementing each web service using the Spiel
framework, Spiel will generate interface documentation for your web service
when the service is requested without any parameters. For example, this
screenshot shows what the employees/read.php web service from the
EmployeeManager example returns when it is requested:

![Example](/example.png "Example of web service documentation returned by Spiel")

When you actually want to invoke a web service, you simply add the "x" parameter
to your request (and specify any other required parameters). For example, to
invoke the employees/read.php web service shown above, you could request the
following via your web browser or programatically in client-side code:

```
https://yourserver.com/employees/read.php?x
```

In addition to producing interface documentation, Spiel provides the basic
infrastructure common to most web services. For example, each web service can be
configured to require a certain permission in order for it to be accessed, and
Spiel will enforce this by inspecting the requesting client's login. Spiel can
also ensure that any required parameters to invoke a particular web service are
present. Finally, Spiel provides a consistent format into which your returned
data is sent. This JSON structure has the following format:

```JSON
{
    status,
    message,
    data
}
```

###Requirements###
Spiel requires PHP 5.3 or higher and has been tested in an Apache 2 / PHP
environment.

###Documentation###
API documentation is available in the `docs/html` folder (open `index.htm`). In
addition, there are some examples of using the framework in the examples folder:

- **HelloWorld**: Very simple example implementing the ubiquitous "Hello World"
  functionality.
- **EmployeeManager**: More full-fledged example of implementing web services
  using the framework. The web services provide an interface for managing
  employee information stored in an in-memory database on the server. While the
  web services shown are insufficient to completely provide an interface to
  managing employee information (for example, the complete set of "CRUD"
  functions are not available), they are sufficient for illustrating most of the
  features of the Spiel framework.

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

###License###
Spiel uses the MIT License, so you're free to incorporate this software into
whatever you want pretty much without restriction--commercial software, personal
projects, you name it. Attribution is always appreciated but isn't required.

###Acknowledgments###
I must acknowledge my blatant pillaging of the Echo 3 framework's API
documentation style as the source of Spiel's own documentation style. Echo 3 is
a stellar framework for creating Rich Internet Applications, and I've used its
JavaScript API extensively on professional projects to produce first-rate web
applications with relative ease (and yes, those same web applications used Spiel
or its precursor to implement their web services).

Check out [Echo 3](http://echo.nextapp.com/)! It's well worth a look.
