<?php

if (empty($argv[1])) {
    throw new \Exception("You need to set your github API key");
}

require_once './../vendor/autoload.php';

$token = $argv[1];
$client = \GraphQLClientPhp\Client\BasicClient::factory(
    'https://api.github.com',
    'graphql',
    $token
);

$results = $client
    ->setVariables(['number' => 5])
    ->addQuery('
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
    ->setVariables(['number' => 5])
    ->addQuery('
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
    ->setVariables(['number' => 5])
    ->addQuery('
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
    ->sendQueries(); // Use promise with parameter at true

var_dump($results);
