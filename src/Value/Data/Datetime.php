<?php

namespace Capriolo\Eavquent\Value\Data;

use Capriolo\Eavquent\Value\Value;

class Datetime extends Value
{
    /**
     * Casting content to date.
     *
     * @var array
     */
    protected $dates = ['content'];
}
