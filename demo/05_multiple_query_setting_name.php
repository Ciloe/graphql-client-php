<?php

if (empty($argv[1])) {
    throw new \Exception("You need to set your github API key");
}

require_once './../vendor/autoload.php';

$token = $argv[1];

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

$client = \GraphQLClientPhp\Client\BasicClient::factory(
    'https://api.github.com',
    'graphql',
    $token,
    $queryParser
);

$client
    ->setName('repositories5')
    ->setVariables(['number' => 5])
    ->addQuery($adapter->getItem('repositories')->get());
$client
    ->setName('repositories6')
    ->setVariables(['number' => 6])
    ->addQuery($adapter->getItem('repositories')->get());
$client
    ->setName('repositories10')
    ->setVariables(['number' => 10])
    ->addQuery($adapter->getItem('repositories')->get());

$results = $client->sendQueries(true);

var_dump($results);
