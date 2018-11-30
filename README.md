# **GraphQL Client PHP**

[![Build Master Status](https://travis-ci.org/Ciloe/graphql-client-php.svg?branch=master)](https://travis-ci.org/Ciloe/graphql-client-php)

## How to install

### Install by **sources**

You can install the project with ssh `git clone git@github.com:Ciloe/graphql-client-php.git` or 
or by https `git clone https://github.com/Ciloe/graphql-client-php.git`

### Install by **composer**

To add at your project, just use this command line : 

`composer require ciloe/graphql-client-php`

## Configuration

At the first time, you need to configure the API information (host, uri, token).

```php
<?php

require_once './vendor/autoload.php';

$model = new \GraphQLClientPhp\Model\ApiModel('https://api.github.com', '/graphql', 'MyToken');
```

Now you can use the client. See following examples

## Basic used (see [demo Folder](./demo))

Before everything, declare the bridge, this class will calling your API.
You must declare your client too.

```php
<?php

// Some code

$bridge = new \GraphQLClientPhp\Bridge\BasicBridge($model);
$client = new \GraphQLClientPhp\Client\BasicClient(
    $bridge, 
    new GraphQLClientPhp\Parser\QueryBasicParser()
);
```

## Using factory

To create faster a client, you can use the factory function like this.

```php
<?php

require_once './vendor/autoload.php';

$client = \GraphQLClientPhp\Client\BasicClient::factory(
    'https://api.github.com',
    'graphql',
    'MyToken'
);
```

Now you can use the client to call the api with a simple query :

```php
<?php

// Some code

$results = $client->query('query test {user {name}}');
```

See all functions available in the client [here](./src/Client/ClientInterface.php).

## Advanced Use

### Use queries with variables

Now you can use queries by name.

```php
<?php

// Some code

$bridge = new \GraphQLClientPhp\Bridge\BasicBridge($model);
$client = new \GraphQLClientPhp\Client\BasicClient(
    $bridge, 
    new GraphQLClientPhp\Parser\QueryBasicParser()
);

$results = $client
    ->addVariable('variable', true)
    ->query('query test ($variable: Boolean!) {user @include (if: $variable) {name}}');
```

### Use queries cached

```php
<?php

// Some code

$pool = new \Symfony\Component\Cache\Adapter\FilesystemAdapter();
$fileCache = $PATH_TO_CACHE_DIR . 'cache.php';
$queries = $PATH_TO_GRAPHQL_QUERIES . 'queries';
$fragments = $PATH_TO_GRAPHQL_FRAGMENTS . 'fragments';
$adapter = new \Symfony\Component\Cache\Adapter\PhpArrayAdapter($fileCache, $pool);

$service = new \GraphQLClientPhp\Cache\BasicCache(
    $adapter,
    new \GraphQLClientPhp\Parser\QueryBasicParser(),
    ['queries' => $queries, 'fragments' => $fragments]
);
$service->warmUp();
```

This example will generate queries stored in `$queries` folder
with fragment declared in `$fragment` folder. The cache is a 
single php file generated with the array adapter. The cache keys
are the name of file. It must be unique.

You can use the factory to create multiple cache object : 


```php
<?php

// Some code

$DS = DIRECTORY_SEPARATOR;
$fileCache = __DIR__ . $DS . 'Resources' . $DS . 'cache' . $DS . 'cache.php';
$queries = __DIR__ . $DS . 'Resources' . $DS . 'graph' . $DS . 'queries';
$fragments = __DIR__ . $DS . 'Resources' . $DS . 'graph' . $DS . 'fragments';
$queryParser = new \GraphQLClientPhp\Parser\QueryBasicQueryParser();

$cache = \GraphQLClientPhp\Cache\BasicCache::factory(
    $fileCache,
    $queries,
    $fragments,
    $queryParser
);
```

Now you can use queries by name.

```php
<?php

// Some code

$query = $adapter->getItem('myQueryFile');
$fragments = $adapter->getItem(\GraphQLClientPhp\Cache\CacheInterface::CACHED_FRAGMENT_KEY);
$client = new \GraphQLClientPhp\Client\BasicClient(
    $bridge, 
    new GraphQLClientPhp\Parser\QueryBasicParser(),
    $fragments
);

$result = $client->query($query);
```

### Get array results from async query (use promise)

```php
<?php

// Some code

// In your PHP execution, you can stored more than one query.
$client
->setName('myFirstQuery')
->setVariables(['number' => 5])
->addQuery(
    'query ($number:Int!) {
      viewer {
        name
         repositories(last: $number) {
           nodes {
             name
           }
         }
       }
    }'
);

$client
->setName('mySecondQuery')
->addQuery(
    'query {
      viewer {
        name
       }
    }'
);

$results = $client->sendQueries(true); // Use parameter $async to use or not promises.
```
