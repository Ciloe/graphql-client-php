<?php

namespace GraphQLClientPhp\Cursor;

interface CursorInterface
{
    /**
     * Given an offset in a list, returns the cursor to use as a value for
     * the graph "after" argument to paginate from this offset.
     *
     * @param int $cursor
     * @param int $offset
     *
     * @return null|string
     */
    public function getAfterCursor(int $cursor, int $offset = 0): ?string;

    /**
     * @param int $position
     *
     * @return string
     */
    public function encodeCursor(int $position): string;
}
