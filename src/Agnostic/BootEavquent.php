<?php

namespace Capriolo\Eavquent\Agnostic;

use Capriolo\Eavquent\Attribute\Cache;
use Capriolo\Eavquent\AttributeCache;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;

class BootEavquent
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * BootEavquent constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * Booting Eavquent.
     */
    public function boot()
    {
        if (! $this->container) {
            $this->container = new Container;
        }

        $this->registerBindings();

        Container::setInstance($this->container);
    }

    /**
     * Registering contianer bindings.
     */
    public function registerBindings()
    {
        $this->container->bind(AttributeCache::class, Cache::class);

        $this->container->singleton(\Illuminate\Contracts\Cache\Repository::class, function () {
            $store = new FileStore(new Filesystem, __DIR__ . '/../cache');

            return new Repository($store);
        });
    }
}
