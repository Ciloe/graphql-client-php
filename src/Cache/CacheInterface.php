<?php

namespace GraphQLClientPhp\Cache;

use Symfony\Component\Cache\Adapter\PhpArrayAdapter;

interface CacheInterface
{
    const CACHED_FRAGMENT_KEY = 'cachedFragments';

    public function init(): void;

    public function warmUp(): void;

    /**
     * @return PhpArrayAdapter
     */
    public function getWriter(): PhpArrayAdapter;
}
