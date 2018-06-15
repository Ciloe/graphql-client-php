<?php

namespace GraphClientPhp\Cache;

interface CacheInterface
{
    const CACHED_FRAGMENT_KEY = 'cachedFragments';

    public function init();

    public function warmUp();
}
