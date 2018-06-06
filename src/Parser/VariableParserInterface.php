<?php

namespace GraphQLClientPhp\Parser;

interface VariableParserInterface
{
    /**
     * @return array
     */
    public function getVariables(): array;

    /**
     * @param array $variables
     *
     * @return VariableParserInterface
     */
    public function setVariables(array $variables): VariableParserInterface;

    /**
     * @param string                   $key
     * @param array|string|int|boolean $value
     *
     * @return VariableParserInterface
     */
    public function addVariable(string $key, $value): VariableParserInterface;

    /**
     * @return string
     */
    public function parseVariables(): string;

    /**
     * @return VariableParserInterface
     */
    public function purgeVariables(): VariableParserInterface;
}
