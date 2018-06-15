<?php

namespace Test\GraphClientPhp\Parser;

use GraphClientPhp\Parser\QueryBasicQueryParser;
use PHPUnit\Framework\TestCase;

class QueryBasicParserTest extends TestCase
{
    /**
     * @throws \GraphClientPhp\Exception\FileNotFoundException|\GraphQL\Error\SyntaxError
     */
    public function testParseQueryBasic()
    {
        $query = <<<GraphQl
        query country {
            country {
                name
                iso
            }
        }
GraphQl;

        $service = new QueryBasicQueryParser();
        $result = $service->parseQuery($query);
        $this->assertSame($query, $result, "The expected query must be the same like the result");
    }

    /**
     * @throws \GraphClientPhp\Exception\FileNotFoundException|\GraphQL\Error\SyntaxError
     */
    public function testParseQueryBasicWithExistingFragment()
    {
        $expected = <<<GraphQl
        query country {
            ...country
        }
        fragment country on CountryDefinition {
            name
            iso
        }
GraphQl;
        $query = <<<GraphQl
        query country {
            ...country
        }
        fragment country on CountryDefinition {
            name
            iso
        }
GraphQl;

        $service = new QueryBasicQueryParser();
        $result = $service->parseQuery($query);
        $this->assertSame($expected, $result, "The expected query must be the same like the result");
    }

    /**
     * @throws \Exception
     */
    public function testParseQueryWithFragment()
    {
        $expected = <<<GraphQl
        query country {
            ...country
        }
fragment country on CountryDefinition {
  name
  iso
}
GraphQl;
        $query = <<<GraphQl
        query country {
            ...country
        }
GraphQl;
        $fragment = <<<GraphQl
        fragment country on CountryDefinition {
            name
            iso
        }
GraphQl;

        $service = (new QueryBasicQueryParser())->setFragments([$fragment]);
        $result = $service->parseQuery($query);
        $this->assertSame($expected, $result, "The expected query must be the merge between query and fragment");
    }

    /**
     * @throws \Exception
     */
    public function testParseQueryWithMultipleFragments()
    {
        $expected = <<<GraphQl
        query country {
            country1: country { ...country }
            country2: country { ...country }
        }
fragment country on CountryDefinition {
  name
  iso
}
GraphQl;
        $query = <<<GraphQl
        query country {
            country1: country { ...country }
            country2: country { ...country }
        }
GraphQl;
        $fragment = <<<GraphQl
        fragment country on CountryDefinition {
            name
            iso
        }
        fragment movie on MovieDefinition {
            title
        }
GraphQl;

        $service = (new QueryBasicQueryParser())->setFragments([$fragment]);
        $result = $service->parseQuery($query);
        $this->assertSame($expected, $result, "The expected query must be the merge between query and fragment");
    }

    /**
     * @expectedException \GraphClientPhp\Exception\FileNotFoundException
     * @expectedExceptionCode 461
     * @expectedExceptionMessage The graph fragment notExisting does not exist
     *
     * @throws \Exception
     */
    public function testParseQueryWithEmptyFragment()
    {
        $query = <<<GraphQl
        query country {
            country1: country { ...country }
            country2: country { ...notExisting }
        }
GraphQl;
        $fragment = <<<GraphQl
        fragment country on CountryDefinition {
            name
            iso
        }
GraphQl;

        $service = (new QueryBasicQueryParser())->setFragments([$fragment]);
        $service->parseQuery($query);
    }

    /**
     * @throws \Exception
     */
    public function testGetQueryName()
    {
        $service = new QueryBasicQueryParser();
        $query = <<<GraphQl
        query country {
            name
        }
GraphQl;
        $this->assertSame('country', $service->getQueryFirstName($query), "With one query with name, you must have this name");

        $query = <<<GraphQl
        mutation country {
            name
        }
GraphQl;
        $this->assertSame('country', $service->getQueryFirstName($query), "With one mutation with name, you must have this name");

        $query = <<<GraphQl
        query country {
            name
        }
        query movie {
            title
        }
GraphQl;
        $this->assertSame('country', $service->getQueryFirstName($query), "With two queries the first name query is the query name");

        $query = <<<GraphQl
        {
            country {
                name
            }
        }
GraphQl;
        $this->assertNull($service->getQueryFirstName($query), "Without queries declaration the name must be null");
    }
}
