<?php

namespace Capriolo\Eavquent\Attribute;

use Capriolo\Eavquent\AttributeCache;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Collection;

class Cache implements AttributeCache
{
    /**
     * The cache repository.
     *
     * @var Repository
     */
    protected $cache;

    /**
     * The cache key.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * AttributeCache constructor.
     *
     * @param Repository $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
        $this->cacheKey = eav_config('cache_key');
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return $this->cache->has($this->cacheKey);
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->cache->get($this->cacheKey);
    }

    /**
     * {@inheritdoc}
     */
    public function set(Collection $attributes)
    {
        $this->flush();

        return $this->cache->forever($this->cacheKey, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->cache->forget($this->cacheKey);
    }
}
