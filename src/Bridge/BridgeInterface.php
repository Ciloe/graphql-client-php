<?php

namespace GraphQLClientPhp\Bridge;

interface BridgeInterface
{
    /**
     * Sent the json query to the API by using a client.
     *
     * @param string $name
     * @param string $string
     *
     * @return \stdClass
     */
    public function query(string $name, string $string): \stdClass;

    /**
     * Sent the array of query to the API by using the promise.
     *
     * @param array $queries
     *
     * @return \stdClass[]
     */
    public function queryAsync(array $queries): array;
}
