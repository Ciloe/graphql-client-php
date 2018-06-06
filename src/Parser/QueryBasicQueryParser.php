<?php

namespace GraphQLClientPhp\Parser;

use GraphQLClientPhp\Exception\FileNotFoundException;
use GraphQL\Language\AST\FragmentDefinitionNode;
use GraphQL\Language\AST\FragmentSpreadNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\Parser;
use GraphQL\Language\Printer;
use GraphQL\Language\Visitor;

class QueryBasicQueryParser implements QueryParserInterface
{
    /**
     * @var array
     */
    private $loadedFragments = [];

    /**
     * @var array
     */
    private $fragments = [];

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function getSeparatedFragments(string $source): array
    {
        $fragments = [];
        Visitor::visit(Parser::parse($source), [
            'leave' => [
                NodeKind::FRAGMENT_DEFINITION => function (
                    FragmentDefinitionNode $node
                ) use (&$fragments) {
                    $fragments[$node->name->value] = Printer::doPrint($node);
                },
            ],
        ]);

        return $fragments;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function getCallingFragments(string $source): array
    {
        $names = [];
        Visitor::visit(Parser::parse($source), [
            'leave' => [
                NodeKind::FRAGMENT_SPREAD => function (
                    FragmentSpreadNode $node
                ) use (&$names) {
                    if (!empty($node->name)) {
                        $names[] = $node->name->value;
                    }
                },
            ],
        ]);

        return $names;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception|FileNotFoundException
     */
    public function recursiveAddFragments(string $query): string
    {
        if ($names = $this->getCallingFragments($query)) {
            foreach ($names as $name) {
                if (!in_array($name, $this->loadedFragments)) {
                    if (!array_key_exists($name, $this->fragments)) {
                        throw new FileNotFoundException(
                            "The graph fragment $name does not exist"
                        );
                    }
                    $fragmentFile = $this->fragments[$name];

                    $this->loadedFragments[] = $name;
                    $parsedFragment = $this->recursiveAddFragments(
                        $fragmentFile
                    );
                    $query .= sprintf("\n%s", $parsedFragment);
                }
            }
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     * @throws \Exception
     */
    public function setFragments(array $inputFragments): QueryParserInterface
    {
        foreach ($inputFragments as $fragment) {
            $separatedFragments = $this->getSeparatedFragments($fragment);
            foreach ($separatedFragments as $name => $separatedFragment) {
                $this->fragments[$name] = $separatedFragment;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     * @throws \Exception
     */
    public function existingFragments(string $query): QueryParserInterface
    {
        $existingFragments = $this->getSeparatedFragments($query);
        $this->fragments = array_merge(
            $this->fragments,
            $existingFragments
        );
        $this->loadedFragments = array_keys($existingFragments);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception|FileNotFoundException|\GraphQL\Error\SyntaxError
     */
    public function parseQuery(string $query): string
    {
        $query = $this->existingFragments($query)
            ->recursiveAddFragments($query);
        $this->loadedFragments = [];

        return $this->checkParsing($query);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \GraphQL\Error\SyntaxError
     */
    public function checkParsing(string $query): string
    {
        Parser::parse($query);

        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function getQueryFirstName(string $query): ?string
    {
        $names = [];
        Visitor::visit(Parser::parse($query), [
            'leave' => [
                NodeKind::OPERATION_DEFINITION => function (
                    OperationDefinitionNode $node
                ) use (&$names) {
                    if (!is_null($node->name)) {
                        $names[] = $node->name->value;
                    }
                },
            ],
        ]);

        return array_shift($names);
    }
}
