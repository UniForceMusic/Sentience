# Sentience

## 1. Introduction

Sentience is a lightweight, yet versatile api framework created as a personal project of Koen de Wolf (UniForceMusic), and later adopted as the framework for the service of The Rent Friend.

### 1.1 In house written features (the philosofy of Sentience)
Sentience was created with the idea of writing all the features required by a modern web framework yourself. Unless you spend years working with a framework you'll probably never know all the functionality in the framework. It's also easy to get stuck in design patterns prevalent commonly found in PHP frameworks. Sentience takes its inspiration not only from other PHP frameworks, but also Golang (Chi, Bun ORM) and Python (Django and Flask).

### 1.2 Why choose Sentience?
The philosophy of Sentience is "If it can be written, it shall be written". Or rather "If i can write it myself, why use an external library for it". This creates a framework where code is written with intent instead of importing an entire library just to use one functionality.

As you'll notice, the composer.json does not require any packages except for PHPUnit as a dev package. This adds the flexibility of being able to import packages, without having a bunch of packages that need to be included by default.

### 1.3 Features of Sentience
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
- Class importer

### 1.4 Setup guide

Note: Make sure a local version of PHP is installed and added to the path. The easiest way to install PHP for each operating system is:

#### Windows
Download the following applications and install them in order:
- XAMPP: https://www.apachefriends.org/
- Composer: https://getcomposer.org/

#### Mac OS
Make sure Brew is installed and run the following command:
`brew install php@8.2`

After it is installed, run the following commands to install Composer
```
curl -sS https://getcomposer.org/installer | php 
mkdir -p /usr/local/bin 
mv composer.phar /usr/local/bin/composer 
chmod +x /usr/local/bin/composer 
```

#### Linux
As there are too many Linux distributions to cover. It is recommended googling for your specific version. Here the process will be described for Debian based distributions:

Run the following commands in order:
```
sudo apt upgrade
sudo apt update
sudo apt install --no-install-recommends php8.1
sudo apt install php-cli unzip
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

#### 1.4.1 Docker
The use of Docker is highly encouraged to run this application, but absolutely not required ofcourse. If you wish to use Docker to run this project, simply type the following command: (PHP => 8.1 must be installed locally)
```
PHP index.php docker/init
```

The other commands to control the Docker instance are:
```
PHP index.php docker/up
PHP index.php docker/down
PHP index.php docker/rebuild
```
These should be self explanatory.

#### 1.4.2 Local

If you want to run Sentience locally, the easiest way to install the required dependencies locally is by installing Xampp (or another Apache MySQL PHP alternative). Composer is required for package management.

As soon as MySQL is running, the following commands need to be run in order to initialize the application
```
composer install

PHP index.php database/create
PHP index.php database/init
PHP index.php database/migrate
```

To start the HTTP server, run the following command to start the built-in PHP development server
```
PHP index.php server/start
```

## 2. Creating routes and commands

Sentience uses callable functions that are executed when the incoming request or command matches a string defined in the `routes.php` or `commands.php` file.

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
    protected array $columns = [
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

`->hydrateByField('field', 'value')`

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

`protected array $columns`:
An assosiative array where the key is the name of the class property, and the value is the name of the corresponding database column.

`protected array $onSave`:
An assosiative array where the key is the name of the class property, and the value is a callable which is executed on insert or update.

If the method exists in the class, it will execute this method. If it does not exist, but a globally declared function does, it will execute this function. Static methods are also supported by using an array as a callable. Lambda's are not supported.

`protected function registerRelations()`:
Allows you to register relations by calling the protected method `registerRelation`.

The model class has more built in methods you can override in your model to make your life easier.

`onInsertOrUpdate()`: Runs each time an insert or update is triggered. Useful for generating or manipulating data like calculated columns.

`validate()`: Runs each time an insert or update is triggered. Useful for preventing wrong data from being inserted or a database error being triggered.

`testValidate()`: Runs the validate function and returns null if valid, a string containing the error if invalid.

`exportAsRecord()`: returns an assosiative array with the keys being the column names, and the values being the raw values from the database,

#### 3.2.3 Supported variable types

- bool
- int
- float
- string
- DateTime

All variable types are nullable. Except for the property which holds the primary key

### 3.3 HTTP client

Sentience offers a lightweight alternative for libraries like Guzzle.

#### 3.3.1 Creating a new client

To create a new HTTP client you need to initialize a new `\src\httpclient\HttpClient` class. 

In the HTTP client, a number of properties can be set that the requests created from the client will inherit.

The HttpClient class has the following methods:
```
->baseUrl()
->path()
->url() (overrides ->baseUrl() and ->path())
->method()
->parameters()
->parameter()
->headers()
->header()
->cookies()
->cookie()
->body()
->json()
->timeout()
->timeoutMs()
->retryCount()
->customOption('key', 'value')
```

The `customOption()` method is able to manually override or set new cURL options.

#### 3.3.1 Creating a new request

Call the function `->createRequest()` on the initialized HTTP client.

This returns a `\src\httpclient\HttpRequest` object, which can be modified using method chaining.

The HttpRequest class has same methods as HttpClient, except for one addition:
```
->execute()
```

#### 3.3.3 Executing a request

When the request is ready to be executed. The `->execute()` method can be called, and an `\src\httpclient\HttpResponse` object will be returned.

#### 3.3.4 Handling the response

The HttpResponse class has the following methods:
```
->getHttp()
->getCode()
->getUrl()
->getHeaders()
->getBody()
->getJson()
->getXml()
->getCurlInfo('key')
->asAssoc()
```

### 3.4 Response class

When running a method designed for an HTTP route, the \src\app\Response class is usually imported by default. The response object contains a big selection of static methods with automatic serialization to make it easier to send responses.

The Response class has static methods that corresponds with HTTP status codes:
```
Response::ok()                  // 200
Response::created()             // 201
Response::notFound()            // 404
Response::internalServerError() // 500
```

These are just some examples. All the documented status code from the Mozilla docs are integrated (https://developer.mozilla.org/en-US/docs/Web/HTTP/Status)

By default arrays and objects are serialized into json. Strings, integers and floats are returned as txt. If you wish to override this, include the mime type as the second argument:
```
Response::ok('iVBORw0KGgoAAAA', MimeTypes::get('image/png'))

Note: Once a static Response method is called, the application exits.
```

### 3.5 Stdio class

When running a method designed for the command line, the \src\app\Stdio class is usually imported by default. The Stdio class has static methods that make writing to the STDOUT and STDERR easier.

The methods are:
```
Stdio::print()
Stdio::printLn()
Stdio::printF()
Stdio::printFLn()

Stdio::error()
Stdio::errorLn()
Stdio::errorF()
Stdio::errorFLn()
```

Note: Once a static Stdio method is called, the application does not quit. You need to place a return or exit in your method.

### 3.6 Incoming request objects

Sentience offers a way to unserialize incoming requests. This gives the following benefits:
- Intellisense autocompletion
- Input checking before any method code is executed
- Re-use the same request for different methods

To create a new request, create a new file in src/requests. Use the template below for the file contents:
```
<?php

namespace src\requests;

class ClassNameHere extends Request
{
    public mixed $classProperty;
    
    protected array $properties = [
        '<property name>' => '<key>'
    ]
}
```

Create public properties for all the data you want to access. Add these properties to the protected array `properties` as `<property name>` and the key where the class should look as `<key>`.

The key can reference data from headers, parameters, vars and json by using the prefix:
```
// Allowed types: mixed, string, ?string, array, ?array
'<property name>' => 'header:name

// Allowed types: mixed, string, ?string, array, ?array
'<property name>' => 'parameter:name'

// Allowed types: mixed, string, ?string
'<property name>' => 'var:name'

// Allowed types: mixed, null, bool, int, float, string, array, object, src\requests\Request, src\requests\Request[]
'<property name>' => 'json:nested_key.using.dot.notation'

// Allowed types: mixed, string, ?string
'<property name>' => 'formdata:name'
```

The public class properties can later by accessed once the payload has been parsed and the values from the payload are assigned to the correct properties.

#### 3.6.1 Nesting

If you want to parse a nested array of objects, you'd create a new property `public array $nestedRequests`.

In the codecomment of the property, include the type of request class the array should contain.
```
/**
 * @var RequestClass[]
 */
public array $nestedRequests;
```

The array will be hydrated with the request classes.

#### 3.6.2 Using the request object in a route

To include the request object in a route, go to the `routes.php` file and add the following method at the end of a route:
```
->setRequest(ClassNameHere::class)
```

In your method, include the following code at the top:
```
/** @var ClassNameHere $parsedRequest */
$parsedRequest = $request->getRequest();
```

### 3.7 Migrations

Sentience supports database migrations. Create a new file ending with .sql in the migrations folder. It executes the files in alphabetical order. Sentience recommends using a number system such as:

`yyyymmddhhmmss_migration_name.sql`, for example `20220228145804_remove_name_column.sql`

When a new model has been created, Sentience supports a way to create a database migration for that model. Run the command:

`php index.php models/init --class={MODEL_NAME}` replacing MODEL_NAME with the class name of your model. It creates and executes the migration.

### 3.8 Static files

Static files are served from the `static` directory. Most filetypes are supported, provided they have a valid mimetype.

The .env file has a number of parameters that influence the behaviour of static files:
```
FILES_ENABLED=true      # Enable or disable serving of static files
FILES_CORS=true         # Add CORS middleware to static files
FILES_HIDE_ROUTE=false  # Hide file routes on the route not found page
FILE_CACHE=2592000      # Cache time-to-live in seconds (0 to disable cache)
```

### 3.9 Pages

Sentience offers a simple way to create routes that serve html. These pages are located in the `src/pages` directory.

Path variables are supported using the {bracket} syntax, used for creating api routes.

For example, these are all valid paths:
```
(index defaults to '/')
index.php

(using template variables as a folder name)
{id}/index.php
{id}/edit.php

(using templates variables as file names)
users/{id}.php
```

These contents of these variables are available by reading a variable of the same name on the page.

For example: `{userId}` will be available as `$userId` within the PHP tags.

Note: Only .php, .html and .html files are supported.

#### 3.9.1 Components

Reusable can be created in the `src/components` directory. Variables can be passed to these components using the optional second parameter as an array.

This is what the syntax looks like to import a component:
```
<?php component('<component name>', ['name' => 'custom component']) ?>
```

Within the component, the parameter `name` is usable as the variable `$name` within PHP tags.

Note: all components, and folders containing components must be in lowercase. Otherwise operating systems with a case sensitive file system will not be able to load the components.

#### 3.9.2 Page settings

The .env file has a number of parameters that influence the behaviour of pages:
```
PAGES_ENABLED=true              # Enables or disables serving of pages
PAGES_CORS=true                 # Add CORS middleware to static files
PAGES_HIDE_ROUTE=false          # Hide page routes on the route not found page
PAGES_ALLOWED_FILE_EXTENSIONS   # Allowed file extensions to serve as a page
```

### 3.10 DotEnv

Sentience has its own .env parser. It adds support for mixed arrays, booleans and strict strings.

An array looks like this:
```
ARRAY=[1, 2, null, 'string', "template string"]
```

Single quote strings don't compile templated variables. Double quoted strings compile variables.
Variables are added like this:
```
NAME='Sentience'
DESCRIPTION="${NAME} is a lightweight api framework"
```

And `DESCRIPTION` will compile to "Sentience is a lightweight api framework".

Support for multiline strings is not supported as of yet

### 3.11

Sentience has a class importer that makes importing an array of classes from a directory easier.

The class can be found in `src/importers/ClassImporter.php`

The class importer has two methods:
- `ClassImporter::importAsString` (imports the classes as an array of strings including the relative namespace)
- `ClassImporter::importAsClass` (imports the classes as initialized classes)

#### 3.11.1 Code examples:

ClassImporter::importAsString:
```
ClassImporter::importAsClass(
    BASEDIR,
    '/src/apis',
    [
        ApiAbstract::class,
        ApiInterface::class
    ]
);
```

ClassImporter::importAsClass:
```
ClassImporter::importAsClass(
    BASEDIR,
    '/src/apis',
    [
        ApiAbstract::class,
        ApiInterface::class
    ],
    [
        'httpClient' => new HttpClient()
    ]
);
```

## Good luck creating an application with Sentience!
