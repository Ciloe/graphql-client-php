<?php

namespace Test\Mock;

use GraphQLClientPhp\Bridge\BridgeInterface;

class BridgeMock implements BridgeInterface
{
    /**
     * @var array
     */
    private $results;

    /**
     * @param array $results
     *
     * @return BridgeMock
     */
    public function storeResults(array $results): BridgeMock
    {
        $this->results = $results;

        return $this;
    }

    /**
     * @param string $key
     * @param string $result
     *
     * @return BridgeMock
     */
    public function addResult(string $key, string $result): BridgeMock
    {
        $this->results[$key] = $result;

        return $this;
    }

    /**
     * @param string $name
     * @param string $query
     *
     * @return \stdClass
     */
    public function query(string $name, string $query): \stdClass
    {
        if (array_key_exists($name, $this->results) && $query) {
            return json_decode($this->results[$name]);
        }

        return new \stdClass();
    }

    /**
     * @param array $queries
     *
     * @return array
     */
    public function queryAsync(array $queries): array
    {
        $results = [];
        foreach ($queries as $key => $query) {
            $results[$key] = $this->query($key, $query);
        }

        return $results;
    }
}
