<?php

namespace Capriolo\Eavquent;

use Illuminate\Support\Collection;

interface AttributeCache
{
    /**
     * Check if values exist.
     *
     * @return bool
     */
    public function exists();

    /**
     * Get the cache values.
     *
     * @return Collection
     */
    public function get();

    /**
     * Set the cache values.
     *
     * @param Collection $attributes
     * @return void
     */
    public function set(Collection $attributes);

    /**
     * Flush the cache values.
     *
     * @return void
     */
    public function flush();
}
