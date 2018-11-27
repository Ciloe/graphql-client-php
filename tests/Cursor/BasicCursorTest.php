<?php

namespace Cursor;

use GraphQLClientPhp\Cursor\BasicCursor;
use PHPUnit\Framework\TestCase;

class BasicCursorTest extends TestCase
{
    public function testEncodeCursor()
    {
        $this->assertSame(base64_encode('test:1'), (new BasicCursor('test'))->encodeCursor(1));
    }

    public function testGetAfterCursor()
    {
        $page = 1;
        $itemsPerPage = 10;

        $this->assertNull((new BasicCursor('test'))->getAfterCursor(($page - 1) * $itemsPerPage));

        $page = 2;
        $this->assertSame(base64_encode('test:9'), (new BasicCursor('test'))->getAfterCursor(($page - 1) * $itemsPerPage));

        $page = 3;
        $this->assertSame(base64_encode('test:19'), (new BasicCursor('test'))->getAfterCursor(($page - 1) * $itemsPerPage));

        $page = 4;
        $this->assertSame(base64_encode('test:33'), (new BasicCursor('test'))->getAfterCursor(($page - 1) * $itemsPerPage, 4));
    }
}
