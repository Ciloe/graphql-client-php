<?php

namespace GraphQLClientPhp\Cache;

interface CacheInterface
{
    const CACHED_FRAGMENT_KEY = 'cachedFragments';

    public function init(): void;

    public function warmUp(): void;
}
