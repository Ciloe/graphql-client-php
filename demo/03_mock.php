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

$bridge->addResult('anonymous0', json_encode(
    [
        'data' => [
            'viewer' => [
                'name' => 'myName',
                'repositories' => [
                    ['name' => 'test1'],
                    ['name' => 'test2'],
                    ['name' => 'test3'],
                ],
            ],
        ],
    ]
));

$results = $client
    ->setVariables(['number' => 5])
    ->query($adapter->getItem('repositories')->get());

var_dump($results);
