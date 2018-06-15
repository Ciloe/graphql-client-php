<?php

namespace GraphClientPhp\Parser;

interface QueryParserInterface
{
    /**
     * If you declared two fragment in the same file/declaration, this function
     * will separate the two fragments.
     *
     * @param string $source
     *
     * @return array
     */
    public function getSeparatedFragments(string $source): array;

    /**
     * Will return all calling fragment in the query/fragment (using ...myFragment).
     *
     * @param string $source
     *
     * @return array
     */
    public function getCallingFragments(string $source): array;

    /**
     * Will add at you query the using fragments by existing fragments.
     *
     * @param string $query
     *
     * @return string
     */
    public function recursiveAddFragments(string $query): string;

    /**
     * Will set existing fragments in sources/code.
     *
     * @param array $inputFragments
     *
     * @return QueryParserInterface
     */
    public function setFragments(array $inputFragments): QueryParserInterface;

    /**
     * @return array
     */
    public function getFragments(): array;

    /**
     * If you have already add fragment in you query, this function will stored
     * this information.
     *
     * @param string $query
     *
     * @return QueryParserInterface
     */
    public function existingFragments(string $query): QueryParserInterface;

    /**
     * This function will parse qut query with fragments.
     *
     * @param string $query
     *
     * @return string
     */
    public function parseQuery(string $query): string;

    /**
     * This function will check the parsing by using the GraphQl Parser.
     *
     * @param string $query
     *
     * @return string
     */
    public function checkParsing(string $query): string;

    /**
     * Will return the query name
     *
     * @param string $query
     *
     * @return string|null
     */
    public function getQueryFirstName(string $query);
}
