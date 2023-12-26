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

The service class can be found in the root of the Sentience project. By default a database method is included in the application.

The database can be imported by adding it as a method/function arg to your callable. Like this `function methodName(Request $request, \src\database\Database $database)`. Make sure the datatype of the service method and the method/function arg match to prevent any unexpected errors.

## 3 Feature documentation

### 3.1 Database / Querybuilder

The database class is a wrapper class built on top of PHP's built in PDO. The wrapper adds support for transactions and debug printing. The logs print to STDERR by default to make them visible in the logs of the built in HTTP server.

Raw queries can be executed by calling `$database->exec()` which takes the query as its first argument, and an array of prepared parameters as a second. An array of prepared parameters is optional.

The easiest way to execute queries however is the `$database->query()` method. It returns a query object that supports method chaining to build and execute the full query.

Here is a list of methods that the query class supports:
```
->table('table')
->model(\src\models\Model::class)

->columns(['id', 'name'])
->values([
    'id' => 1,
    'name' => 'John Doe'
])

->where('id',
    \src\database\queries\Query::EQUALS,
    5
)
->and()
->where(
    'name',
    \src\database\queries\Query::IN_ARRAY,
    ['John']
)
->or()
->where(
    'name',
    \src\database\queries\Query::LIKE,
    \src\database\queries\Query::wildcard('Doe')
)

->orderBy(
    'id',
    \src\database\objects\OrderBy::ASC
)
            
->limit(1)
->offset(1)    

->select()
->selectAssoc()
->selectAssocs()
->selectModel()
->selectModels()
->count()
->exists()
->selectSql()

->insert()
->insertWithLastId()
->insertSql()

->update()
->updateSql()

->delete()
->deleteSql()
```

There are some methods of the query class that the documentation doesn't cover. These methods are for functionality within the model class and should be deemed uninmportant for documentation. If you wish to explore these methods yourself, feel free!

### 3.2 Models

To make database communication easier, Sentience supports models that map to database tables.

To create a new model, create a new file with a name that corresponds to a singular entity of your table. For example a table called `user_identities` should have a model named `UserIdentity`.

Use this example structure below to create a new file:
```
<?php

namespace src\models;

use DateTime;
use src\models\relations\BelongsTo;
use src\models\relations\HasMany;
use src\models\relations\HasOne;
use src\models\relations\ManyToMany;

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKeyPropertyName = 'id';
    protected bool $primaryKeyAutoGenerated = true;
    protected array $fields = [
        'id' => 'id',
        'name' => 'name',
        'profileId' => 'profile_id',
        'createdAt' => 'created_at',
    ];

    protected array $onSave = [
        'name' => 'strtolower'
    ];

    public int $id;
    public string $name;
    public ?int $profileId = null;
    public ?DateTime $createdAt;

    protected function registerRelations(): void
    {
        $this->registerRelation(
            new HasOne(
                Profile::class,
                'profile_id'
            )
        );
    }
}
```

#### 3.2.1 C R U D methods

`->hydrate()`

`->hydrateByField('field', 'value)`

`->hydrateByAssoc($pdoStatement, $assoc)`

`->insert()`

`->update()`

`->delete()`

`->undoDelete()`

#### 3.2.2 Properties and methods

`protected string $table`:
The name of the table in the database.

`protected string $primaryKeyPropertyName`:
The name of the property in this class that holds the primary key value for a record.

`protected bool $primaryKeyAutoGenerated`:
True if the database generates the primary key value. False is the primary key should be generated in code.

`protected array $fields`:
An assosiative array where the key is the name of the class property, and the value is the name of the corresponding database column.

`protected array $onSave`:
An assosiative array where the key is the name of the class property, and the value is a callable which is executed on insert or update.

If the method exists in the class, it will execute this method. If it does not exist, but a globally declared function does, it will execute this function. Static methods are also supported by using an array as a callable. Lambda's are not supported.

`protected function registerRelations()`:
Allows you to register relations by calling this protected method `registerRelation`.

The model class has more built in methods you can override in your model to make your life easier.

`onInsertOrUpdate()`: Runs each time an insert or update is triggered. Useful for generating or manipulating data like calculated columns.

`validate()`: Runs each time an insert or update is triggered. Useful for preventing wrong data from being inserted or a database error being triggered.

`testValidate()`: Runs the validate function and returns null if valid, a string containing the error if invalid.

`exportAsRecord()`: returns an assosiative array with the keys being the column names, and the values being the raw values from the database,

#### 3.2.3 Supported variable types

- bool
- int
- double
- float
- string
- DateTime

All variable types are nullable. Except for the property which holds the primary key

### 3.3 HTTP client

Sentience offers a lightweight alternative for libraries like Guzzle.

#### 3.3.1 Creating a new request

To create a new HTTP request you need to call the static method `new` on the HttpClient class.
```
\src\httpclient\HttpClient::new()
```

This returns a `\src\httpclient\HttpRequest` object, which can be modified using method chaining.

