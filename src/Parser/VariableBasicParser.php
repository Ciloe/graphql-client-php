<?php

namespace GraphClientPhp\Parser;

use GraphClientPhp\Exception\VariableParserException;

final class VariableBasicParser implements VariableParserInterface
{
    /**
     * @var array
     */
    private $variables = [];

    /**
     * {@inheritdoc}
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function setVariables(array $variables): VariableParserInterface
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function addVariable(string $key, $value): VariableParserInterface
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws VariableParserException
     */
    public function parseVariables(): string
    {
        $json = json_encode($this->variables);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new VariableParserException(
                sprintf(
                    "The json %s is not valid. Message : %s.",
                    $json,
                    json_last_error_msg()
                )
            );
        }

        return $json;
    }

    /**
     * @return self
     */
    public function purgeVariables(): VariableParserInterface
    {
        $this->variables = [];

        return $this;
    }
}
