<?php

if (empty($argv[1])) {
    throw new \Exception("You need to set your github API key");
}

require_once './../vendor/autoload.php';

$token = $argv[1];
$model = new \GraphQLClientPhp\Model\ApiModel('https://api.github.com', 'graphql', $token);
$bridge = new \GraphQLClientPhp\Bridge\BasicBridge($model);
$client = new \GraphQLClientPhp\Client\BasicClient($bridge, new GraphQLClientPhp\Parser\QueryBasicQueryParser());

$results = $client
    ->setVariables(['number' => 5])
    ->query('
        query ($number:Int!) {
          viewer {
            name
             repositories(last: $number) {
               nodes {
                 name
               }
             }
           }
        }
    ');

var_dump($results);
