<?php

namespace Test\GraphQLClientPhp\Parser;

use GraphQLClientPhp\Parser\VariableBasicParser;
use PHPUnit\Framework\TestCase;

class VariableBasicParserTest extends TestCase
{
    public function testParseVariables()
    {
        $service = new VariableBasicParser();
        $json = $service->setVariables([
            'testInt' => 1,
            'testString' => "MY_GRAPH_FILTER",
            'testArrayInt' => [5, 6, 7],
            'testBoolean' => true,
            'testArrayString' => ["FILTER_1", "FILTER_2", "FILTER_3"],
        ])
        ->parseVariables();

        $this->assertEquals(
            '{"testInt":1,"testString":"MY_GRAPH_FILTER","testArrayInt":[5,6,7],"testBoolean":true,"testArrayString":["FILTER_1","FILTER_2","FILTER_3"]}',
            $json
        );
    }
}
