<?php

namespace Capriolo\Eavquent\Value\Data;

use Capriolo\Eavquent\Value\Value;

class Boolean extends Value
{
    /**
     * Atrribute casting.
     */
    protected $casts = [
        'content' => 'boolean',
    ];
}
