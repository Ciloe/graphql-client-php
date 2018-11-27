<?php

namespace GraphQLClientPhp\Cursor;

class BasicCursor implements CursorInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getAfterCursor(int $cursor, int $offset = 0): ?string
    {
        if ($cursor + $offset <= 1) {
            return null;
        }

        return $this->encodeCursor($cursor - 1 + $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function encodeCursor(int $position): string
    {
        return base64_encode(sprintf('%s:%d', $this->prefix, $position));
    }
}
