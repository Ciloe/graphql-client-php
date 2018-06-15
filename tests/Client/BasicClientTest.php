<?php

namespace Test\Client;

use GraphClientPhp\Client\BasicClient;
use GraphClientPhp\Parser\QueryBasicQueryParser;
use PHPUnit\Framework\TestCase;
use Test\Mock\BridgeMock;

class BasicClientTest extends TestCase
{
    /**
     * @expectedException \GraphClientPhp\Exception\FileNotFoundException
     * @expectedExceptionCode 461
     * @expectedExceptionMessage You must add a query to generate
     */
    public function testErrorNoQueries()
    {
        $service = new BasicClient(new BridgeMock(), new QueryBasicQueryParser());
        $service->generateQuery();
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
            ->generateQuery($query)
            ->getResult('test');

        $this->assertEquals(json_decode($expected), $result);
        $this->assertEmpty($service->getQueries());
    }

    /**
     * @throws \GraphClientPhp\Exception\FileNotFoundException
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
            ->generateQuery($query)
            ->getResult('test');

        $this->assertEquals(json_decode($expected), $result);
        $this->assertEmpty($service->getQueries());
    }

    /**
     * @throws \GraphClientPhp\Exception\FileNotFoundException
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
            ->generateQuery($queryCountry)
            ->generateQuery($queryMovie)
            ->getResults(true);

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
            ->generateQuery($queryCountry)
            ->setName('movie')
            ->generateQuery($queryMovie)
            ->getResults(true);

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
            ->getResults();

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
            ->getResult('test');

        $this->assertEquals(json_decode($expectedCountry), $result);
        $this->assertNotEmpty($service->getQueries());
        $this->assertArrayNotHasKey('test', $service->getQueries());
    }
}
