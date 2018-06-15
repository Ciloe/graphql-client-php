<?php

namespace GraphClientPhp\Client;

use GraphClientPhp\Bridge\BridgeInterface;
use GraphClientPhp\Exception\FileNotFoundException;
use GraphClientPhp\Exception\VariableParserException;
use GraphClientPhp\Parser\QueryParserInterface;
use GraphClientPhp\Parser\VariableBasicParser;
use GraphClientPhp\Parser\VariableParserInterface;

class BasicClient implements ClientInterface
{
    /**
     * @var BridgeInterface
     */
    private $bridge;

    /**
     * @var QueryParserInterface
     */
    private $parser;

    /**
     * @var array
     */
    private $fragments;

    /**
     * @var array
     */
    private $queries = [];

    /**
     * @var VariableParserInterface
     */
    private $variables;

    /**
     * @var string|null
     */
    private $queryName = null;

    /**
     * @var string|null
     */
    private $preparedQuery = null;

    /**
     * @var int
     */
    private $anonymousCounter = 0;

    /**
     * GraphClient constructor.
     *
     * @param BridgeInterface      $bridge
     * @param QueryParserInterface $parser
     * @param array                $fragments
     */
    public function __construct(
        BridgeInterface $bridge,
        QueryParserInterface $parser,
        array $fragments = []
    ) {
        $this->bridge = $bridge;
        $this->parser = $parser;
        $this->fragments = $fragments;
        $this->variables = new VariableBasicParser();
    }

    /**
     * {@inheritdoc}
     */
    final public function setName(string $name): ClientInterface
    {
        $this->queryName = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function getVariables(): array
    {
        return $this->variables->getVariables();
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    final public function setVariables(array $variables): ClientInterface
    {
        $this->variables->setVariables($variables);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    final public function addVariable(string $key, $variable): ClientInterface
    {
        $this->variables->addVariable($key, $variable);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    final public function setQueries(array $queries): ClientInterface
    {
        $this->queries = $queries;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function getQuery(string $key)
    {
        if (array_key_exists($key, $this->queries)) {
            return $this->queries[$key];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     * @throws FileNotFoundException|\Exception
     */
    public function generateQuery(string $query = null): ClientInterface
    {
        if (is_null($this->preparedQuery) && is_null($query)) {
            throw new FileNotFoundException(
                "You must add a query to generate witch one"
            );
        } elseif (!is_null($this->preparedQuery)) {
            $query = $this->preparedQuery;
        }
        $this->parseQuery($query);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     * @throws VariableParserException
     */
    public function parseQuery(string $query): ClientInterface
    {
        $arrayQuery = [
            'query' => sprintf(
                '%s',
                addslashes(preg_replace('@\s+@', ' ', $query))
            ),
        ];

        if (!empty($this->variables->getVariables())) {
            $arrayQuery['variables'] = $this->variables->parseVariables();
            $this->variables->purgeVariables();
        }

        if (!is_null($this->queryName)) {
            $name = $this->queryName;
            $this->queryName = null;
        } else {
            $name = $this->parser->getQueryFirstName($query);
            if (is_null($name)) {
                $name = 'anonymous' . $this->anonymousCounter;
                $this->anonymousCounter++;
            }
        }

        $this->queries[$name] = json_encode($arrayQuery);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function prepare(string $query): ClientInterface
    {
        $this->preparedQuery = $this->parser
            ->setFragments($this->fragments)
            ->parseQuery($query);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return \stdClass|null
     */
    public function getResult(string $queryName = null)
    {
        $result = null;
        $keys = array_keys($this->queries);
        if (is_null($queryName)) {
            $queryName = array_pop($keys);
        }

        $result = $this->bridge->query(
            $this->queryName ?? $queryName,
            $this->queries[$queryName]
        );

        unset($this->queries[$queryName]);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults(bool $async = false): array
    {
        if ($async) {
            $results = $this->bridge->queryAsync($this->queries);
            $this->queries = [];

            return $results;
        }

        $results = [];
        foreach ($this->queries as $key => $query) {
            $results[$key] = $this->bridge->query($key, $query);
        }
        $this->queries = [];

        return $results;
    }
}
