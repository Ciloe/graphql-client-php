# **Graph Client PHP**

[![Build Master Status](https://travis-ci.org/Ciloe/graph-client-php.svg?branch=master)](https://travis-ci.org/Ciloe/graph-client-php)

## How to install

### Install by **sources**

Use `git clone git@github.com:Ciloe/graph-client-php.git` or 
by https `git clone https://github.com/Ciloe/graph-client-php.git`

### Install by **composer**

In your terminal, at your project racine, 
use this following command : 

`composer require ciloe/graph-client-php`

## Configuration

You need to configure you api information.

```php
<?php

require_once './vendor/autoload.php';

$model = new \GraphClientPhp\Model\ApiModel('https://api.github.com', '/graphql', 'MyToken');
```

Now you can going to basic use.

## Basic used (see [demo Folder](./demo))

Configure the bridge, this class will calling your API.

```php
<?php

// Some code

$bridge = new \GraphClientPhp\Bridge\BasicBridge($model);
   
$client = new \GraphClientPhp\Client\BasicClient(
    $bridge, 
    new GraphClientPhp\Parser\QueryBasicParser()
);
```

Now you can use the client. For example to call the api with
a simple query :

```php
<?php

// Some code

$results = $client
    ->generateQuery('query test {user {name}}')
    ->getResult();
```

See all functions available in the client [here](./src/Client/ClientInterface.php).

## Advanced Use

### Get array results from promise

```php
<?php

// Some code

// In your PHP execution, you can stored more than one query.
$client
->setName('myFirstQuery')
->setVariables(['number' => 5])
->generateQuery(
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
->generateQuery(
    'query {
      viewer {
        name
       }
    }'
);

$results = $client->getResults(true); // Use parameter $async to use or not promises.
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

$service = new \GraphClientPhp\Cache\BasicCache(
    $adapter,
    new \GraphClientPhp\Parser\QueryBasicParser(),
    ['queries' => $queries, 'fragments' => $fragments]
);
$service->warmUp();
```

This example will generate queries stored in `$queries` folder
with fragment are declared in `$fragment` folder. The cache is a 
single php file generated with the array adapter. The cache keys
are the name of file. It must be unique.

Now you can use queries by name.

```php
<?php

// Some code

$fragments = $adapter->getItem(\GraphClientPhp\Cache\BasicCache::CACHED_FRAGMENT_KEY);
$query = $adapter->getItem('myQueryFile');
$client = new \GraphClientPhp\Client\BasicClient(
    $bridge, 
    new GraphClientPhp\Parser\QueryBasicParser(),
    $fragments
);

$result = $client
->generateQuery($query)
->getResult();
```
