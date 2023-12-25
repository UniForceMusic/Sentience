# Sentience

## 1. Introduction

Sentience is a lightweight, yet versatile api framework created as a personal project of Koen de Wolf (UniForceMusic), and later adopted as the framework for the service of The Rent Friend.

### 1.1 In house written features (the philosofy of Sentience)
Sentience was created with the idea of writing all the features required by a modern web framework yourself. Unless you spend years working with a framework you'll probably never know all the functionality in the framework. It's also easy to get stuck in design patterns prevalent commonly found in PHP frameworks. Sentience takes its inspiration not only from PHP frameworks, but also Golang (Chi, Bun orm) and Python (Django and Flask).

### 1.2 Features of Sentience
- DotEnv support (custom implementation that supports mixed type arrays)
- HTTP server / command line application in one
- Controllers with methods
- Middleware
- Automatic CORS handling
- Static file hosting
- Automatic exception handling (returns code 500 including error as json)
- Database / Models
    - Migrations
    - Query builder (MySQL, Postgres in progress)
    - Object relational model
    - Models (with built in database connectivity using the database class)
- HTTP client
    - Build request using method chaining
- Custom request objects
    - Unmarshal json, xml etc. into an object for auto completion
    - Supports nested request objects
- Custom response object
    - Automatically marshal array/object to a response object
    - Supports nested response objects

### 1.3 Setup guide

#### 1.3.1 Docker
The use of Docker is highly encouraged to run this application, but absolutely not required ofcourse. If you wish to use Docker to run this project, simply type the following command: (PHP => 8.1 must be installed locally)
```
PHP index.PHP docker/init
```

The other commands to control the Docker instance are:
```
PHP index.PHP docker/up
PHP index.PHP docker/down
PHP index.PHP docker/rebuild
```
These should be self explanatory.

#### 1.3.2 Local

If you want to run Sentience locally, the easiest way to install the required dependencies locally is by installing Xampp (or another Apache MySQL PHP alternative). Composer is required for package management.

As soon as MySQL is running, the following commands need to be run in order to initialize the application
```
composer install

PHP index.PHP database/create
PHP index.PHP database/init
PHP index.PHP database/migrate
```

To start the HTTP server, run the following command to start the built-in PHP development server
```
PHP index.PHP server/start
```

## 2. Creating routes and commands

Sentience uses callable functions that are executed when the incoming request or command matches a string defined in the `routes.PHP` or `commands.PHP` file.

All types of callables are supported.

Defined functions:
```
function stringFunction(): void {
    Response::ok('this defined function is called');
}

$routes = [
    Route::create()
        ->setPath('/callables/string')
        ->setCallable('stringFunction')
        ->setMethods(['GET'])
];
```

Lambda functions:
```
$routes = [
    Route::create()
        ->setPath('/callables/lambda')
        ->setCallable(function () {
            Response::ok('this lambda function is called');
        })
        ->setMethods(['GET'])
];
```

Class methods (non static methods are supported)
```
$routes = [
    Route::create()
        ->setPath('/callables/string')
        ->setCallable([Controller::class, 'method'])
        ->setMethods(['GET'])
];
```

### 2.1 Creating controllers

#### 2.1.1 Creating the controller file
In the `src/controllers` directory the controllers are locoated. When you want to create your own controller. Create a new PHP file with a class inside that extends the `src\controllers\Controller` class.

Command line functions and HTTP request functions differ in the functions and classes they use. Based on the type of request you're routing to your controller, you should import the following classes.

HTTP Request:
```
<?php

namespace src\controllers;

use src\app\Request;
use src\app\Response;

class ClassNameHere extends Controller
{
    public function methodNameHere(Request $request): void
    {
        Response::ok('Hello world!');
    }
}

```

Command line request:
```
<?php

namespace src\controllers;

use src\app\Stdio;

class ClassNameHere extends Controller
{
    public function methodNameHere(array $words, array $flags): void
    {
        Stdio::printLn('Hello world!');
    }
}
```

#### 2.1.2 The request class

To retrieve all incoming request data, the request class is the easiest way to retrieve this information.

The request class has the following methods:
```
$request->getUrl();
$request->getUri();
$request->getQueryString();

$request->getMethod();
$request->getBody();

$request->getHeaders();
$request->getHeader('key');

$request->getParameters();
$request->getParameter('key');

$request->getCookies();
$request->getCookie('key');

$request->getJson();
$request->getXml();

$request->getVars();
$request->getVar('key');
```

The only non self explaining methods are `getVar()` and `getVars()`. Vars are variables defined in the path of the route.

Example:

`/users/{userID}/view` will result in `getVars()` returning:
```
[
    'userID' => '5'
]
```

If `getVar('userID')` returns `'5'`

Note:
The request class supports multiple parameters with the same name. If the query string contains multiple of the same parameter, `getParameter()` or `getParameters()` will return an array. Use $_GET if you wish to have only strings returned.

#### 2.1.3 Words and flags

A command line argument doesn't have headers, cookies or parameters, only a single string as input.

This string is separated into flags and words.

Example:

A command that looks like this
```
php index.php users/create John Doe --userType=premium
```

Will have `John` and `Doe` as words, and `userType` as flag.

#### 2.1.4 Function argument

As described above, `$request`, `$words` and `$flags` are function arguments, but where do they come from you may ask?

Request, words and flags are reserved words, but any other word can be used for service methods to import any type of variable.

