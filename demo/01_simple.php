<?php

if (empty($argv[1])) {
    throw new \Exception("You need to set you github API key");
}

require_once './../vendor/autoload.php';

$token = $argv[1];
$model = new \GraphClientPhp\Model\ApiModel('https://api.github.com', 'graphql', $token);
$bridge = new \GraphClientPhp\Bridge\BasicBridge($model);
$client = new \GraphClientPhp\Client\BasicClient($bridge, new GraphClientPhp\Parser\QueryBasicQueryParser());

$results = $client
    ->setVariables(['number' => 5])
    ->generateQuery('
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
    ')
    ->getResult()
;

var_dump($results);
