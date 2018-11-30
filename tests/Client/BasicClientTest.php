<?php

namespace Test\Client;

use GraphQLClientPhp\Client\BasicClient;
use GraphQLClientPhp\Parser\QueryBasicQueryParser;
use PHPUnit\Framework\TestCase;
use Test\Mock\BridgeMock;

class BasicClientTest extends TestCase
{
    /**
     * @expectedException \GraphQLClientPhp\Exception\FileNotFoundException
     * @expectedExceptionCode 461
     * @expectedExceptionMessage You must add a query to generate
     */
    public function testErrorNoQueries()
    {
        $service = new BasicClient(new BridgeMock(), new QueryBasicQueryParser());
        $service->addQuery();
    }

    public function testQueryWithSettingName()
    {
        $mock = new BridgeMock();
        $service = new BasicClient($mock, new QueryBasicQueryParser());

        $query = <<<GraphQl
        {
            country {
                name
            }
        }
GraphQl;
        $expected = <<<json
        {
            "data": {
                "country": {
                    "name": "Test"
                }
            }
        }
json;

        $mock->storeResults([
            'test' => $expected
        ]);

        $result = $service
            ->setName('test')
            ->addQuery($query)
            ->sendQuery('test');

        $this->assertEquals(json_decode($expected), $result);
        $this->assertEmpty($service->getQueries());

        $result = $service
            ->setName('test')
            ->query($query);

        $this->assertEquals(json_decode($expected), $result);
        $this->assertEmpty($service->getQueries());
    }

    /**
     * @throws \GraphQLClientPhp\Exception\FileNotFoundException
     */
    public function testQueryWithQueryName()
    {
        $mock = new BridgeMock();
        $service = new BasicClient($mock, new QueryBasicQueryParser());

        $query = <<<GraphQl
        query test {
            country {
                name
            }
        }
GraphQl;
        $expected = <<<json
        {
            "data": {
                "country": {
                    "name": "Test"
                }
            }
        }
json;

        $mock->storeResults([
            'test' => $expected
        ]);

        $result = $service
            ->addQuery($query)
            ->sendQuery('test');

        $this->assertEquals(json_decode($expected), $result);
        $this->assertEmpty($service->getQueries());

        $result = $service
            ->query($query);

        $this->assertEquals(json_decode($expected), $result);
        $this->assertEmpty($service->getQueries());
    }

    /**
     * @throws \GraphQLClientPhp\Exception\FileNotFoundException
     */
    public function testAsyncQueries()
    {
        $mock = new BridgeMock();
        $service = new BasicClient($mock, new QueryBasicQueryParser());

        $queryCountry = <<<GraphQl
        query test {
            country {
                name
            }
        }
GraphQl;
        $expectedCountry = <<<json
        {
            "data": {
                "country": {
                    "name": "Test"
                }
            }
        }
json;
        $queryMovie = <<<GraphQl
        query movie {
            movie {
                title
            }
        }
GraphQl;
        $expectedMovie = <<<json
        {
            "data": {
                "movie": {
                    "title": "Test"
                }
            }
        }
json;

        $mock->storeResults([
            'test' => $expectedCountry,
            'movie' => $expectedMovie,
        ]);

        $results = $service
            ->addQuery($queryCountry)
            ->addQuery($queryMovie)
            ->sendQueries(true);

        $this->assertEquals([
            'test' => json_decode($expectedCountry),
            'movie' => json_decode($expectedMovie)
        ], $results);
        $this->assertEmpty($service->getQueries());
    }

    public function testAsyncQueriesWithSettingNames()
    {
        $mock = new BridgeMock();
        $service = new BasicClient($mock, new QueryBasicQueryParser());

        $queryCountry = <<<GraphQl
        {
            country {
                name
            }
        }
GraphQl;
        $expectedCountry = <<<json
        {
            "data": {
                "country": {
                    "name": "Test"
                }
            }
        }
json;
        $queryMovie = <<<GraphQl
        {
            movie {
                title
            }
        }
GraphQl;
        $expectedMovie = <<<json
        {
            "data": {
                "movie": {
                    "title": "Test"
                }
            }
        }
json;

        $mock->storeResults([
            'test' => $expectedCountry,
            'movie' => $expectedMovie,
        ]);

        $results = $service
            ->setName('test')
            ->addQuery($queryCountry)
            ->setName('movie')
            ->addQuery($queryMovie)
            ->sendQueries(true);

        $this->assertEquals([
            'test' => json_decode($expectedCountry),
            'movie' => json_decode($expectedMovie)
        ], $results);
        $this->assertEmpty($service->getQueries());
    }

    public function testQueriesWithSettingQueries()
    {
        $mock = new BridgeMock();
        $service = new BasicClient($mock, new QueryBasicQueryParser());

        $queryCountry = <<<GraphQl
        {
            country {
                name
            }
        }
GraphQl;
        $expectedCountry = <<<json
        {
            "data": {
                "country": {
                    "name": "Test"
                }
            }
        }
json;
        $queryMovie = <<<GraphQl
        {
            movie {
                title
            }
        }
GraphQl;
        $expectedMovie = <<<json
        {
            "data": {
                "movie": {
                    "title": "Test"
                }
            }
        }
json;

        $mock->storeResults([
            'test' => $expectedCountry,
            'movie' => $expectedMovie,
        ]);

        $results = $service
            ->setQueries([
                'test' => $queryCountry,
                'movie' => $queryMovie,
            ])
            ->sendQueries();

        $this->assertEquals([
            'test' => json_decode($expectedCountry),
            'movie' => json_decode($expectedMovie)
        ], $results);
        $this->assertEmpty($service->getQueries());
    }

    public function testQueriesWithSettingQueriesOverrideName()
    {
        $mock = new BridgeMock();
        $service = new BasicClient($mock, new QueryBasicQueryParser());

        $queryCountry = <<<GraphQl
        {
            country {
                name
            }
        }
GraphQl;
        $expectedCountry = <<<json
        {
            "data": {
                "country": {
                    "name": "Test"
                }
            }
        }
json;
        $queryMovie = <<<GraphQl
        {
            movie {
                title
            }
        }
GraphQl;
        $expectedMovie = <<<json
        {
            "data": {
                "movie": {
                    "title": "Test"
                }
            }
        }
json;

        $mock->storeResults([
            'otherName' => $expectedCountry,
            'movie' => $expectedMovie,
        ]);

        $service->setQueries([
            'test' => $queryCountry,
            'movie' => $queryMovie,
        ]);

        $result = $service
            ->setName('otherName')
            ->sendQuery('test');

        $this->assertEquals(json_decode($expectedCountry), $result);
        $this->assertNotEmpty($service->getQueries());
        $this->assertArrayNotHasKey('test', $service->getQueries());
    }

    public function testPrepareAnonymous()
    {
        $mock = new BridgeMock();
        $service = new BasicClient($mock, new QueryBasicQueryParser());

        $query = <<<GraphQl
        {
            movie {
                title
            }
        }
GraphQl;

        $this->assertNull($service->getQuery('anonymous0'));

        $service
            ->addVariable('number', 5)
            ->prepare($query)
            ->addQuery();

        $this->assertSame('{"query":" { movie { title } }","variables":"{\"number\":5}"}', $service->getQuery('anonymous0'));
    }

    public function testVariables()
    {
        $mock = new BridgeMock();
        $service = new BasicClient($mock, new QueryBasicQueryParser());

        $service
            ->setVariables(['string' => 'string'])
            ->addVariable('number', 5)
        ;

        $this->assertSame(['string' => 'string', 'number' => 5], $service->getVariables());
    }

    public function testFactory()
    {
        $service = BasicClient::factory('http://test.fr', 'test', 'MyToken');

        $this->assertSame(BasicClient::class, get_class($service));
    }
}
