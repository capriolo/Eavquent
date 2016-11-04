<?php

namespace Capriolo\Eavquent\Attribute;

class Repository
{
    /**
     * Will return all attributes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Attribute::all();
    }
}
