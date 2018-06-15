<?php

namespace GraphClientPhp\Client;

interface ClientInterface
{
    /**
     * This function is placed to name you query.
     * For example to use a query in a same page with different results.
     * By default, the query name will be "anonymous" + a counter.
     *
     * @param string $name
     *
     * @return ClientInterface
     */
    public function setName(string $name): ClientInterface;

    /**
     * This function return variables stored for the query by an array.
     * It used the VariableBasicParser.
     *
     * @return array
     */
    public function getVariables(): array;

    /**
     * This function is to set at VariableBasicParser the variables as an array.
     *
     * @param array $variables
     *
     * @return ClientInterface
     */
    public function setVariables(array $variables): ClientInterface;

    /**
     * This function is to add at VariableBasicParser the association key => value.
     *
     * @param string $key
     * @param        $variable
     *
     * @return ClientInterface
     */
    public function addVariable(string $key, $variable): ClientInterface;

    /**
     * Will return a array contains all queries not used.
     *
     * @return array
     */
    public function getQueries(): array;

    /**
     * It used to set existing queries (probably in cache) as an array.
     *
     * @param array $queries
     *
     * @return ClientInterface
     */
    public function setQueries(array $queries): ClientInterface;

    /**
     * Get the query by passing key in the stored query array.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getQuery(string $key);

    /**
     * This function generate json sent to the API using parseQuery
     * for prepared query or passing query string.
     *
     * @param string|null $query
     *
     * @return ClientInterface
     */
    public function generateQuery(string $query = null): ClientInterface;

    /**
     * This function will create the json query sent to the server.
     * It parse query and variables if provided.
     *
     * @param string $query
     *
     * @return ClientInterface
     */
    public function parseQuery(string $query): ClientInterface;

    /**
     * This function will get your query and will add fragments stored in
     * the constructor class.
     *
     * @param string $query
     *
     * @return ClientInterface
     */
    public function prepare(string $query): ClientInterface;

    /**
     * If passing $queryName, will return the result of query stored by name.
     * If not, will return the first query result stored in the array of queries.
     *
     * @param string $queryName
     *
     * @return \stdClass|null
     */
    public function getResult(string $queryName = null);

    /**
     * Will use promise at async mode or getResult() to provide results from api.
     *
     * @param bool $async
     *
     * @return \stdClass[]
     */
    public function getResults(bool $async = false): array;
}
