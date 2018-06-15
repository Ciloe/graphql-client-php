<?php

namespace Test\GraphClientPhp\Cache;

use GraphClientPhp\Cache\BasicCache;
use GraphClientPhp\Parser\QueryBasicQueryParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;

class BasicCacheTest extends TestCase
{
    public function testCache()
    {
        $DS = DIRECTORY_SEPARATOR;
        $pool = new FilesystemAdapter();
        $fileCache = __DIR__ . $DS . '..' . $DS . 'Resources' . $DS . 'cache' . $DS . 'cache.php';
        $queries = __DIR__ . $DS . '..' . $DS . 'Resources' . $DS . 'graph' . $DS . 'queries';
        $fragments = __DIR__ . $DS . '..' . $DS . 'Resources' . $DS . 'graph' . $DS . 'fragments';
        $adapter = new PhpArrayAdapter($fileCache, $pool);

        $service = new BasicCache(
            $adapter,
            new QueryBasicQueryParser(),
            ['queries' => $queries, 'fragments' => $fragments]
        );
        $service->warmUp();

        $realQuery = file_get_contents($queries . $DS . 'testReal.graphql');
        $realQueryWithFragment = file_get_contents($queries . $DS . 'testRealWithFragment.graphql');
        $realFragment = file_get_contents($fragments . $DS . 'realFragment.graphql');
        $this->assertSame(
            $realQuery,
            $adapter->getItem('testReal')->get()
        );
        $this->assertSame(
            $realQueryWithFragment . sprintf("\n%s", $realFragment),
            $adapter->getItem('testRealWithFragment')->get()
        );
        $this->assertNull($adapter->getItem('noReal')->get());
        $this->assertNull($adapter->getItem('noRealFragment')->get());
    }
}
