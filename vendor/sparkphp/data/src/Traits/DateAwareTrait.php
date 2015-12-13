<?php

namespace Spark\Data\Traits;

use Carbon\Carbon;

trait DateAwareTrait
{
    /**
     * Get a date object for a property
     *
     * @param string $key
     *
     * @return Carbon
     */
    public function date($key)
    {
        return new Carbon($this->$key);
    }

    /**
     * Get a storage-friendly date string for a property
     *
     * @param string $key
     *
     * @return string
     */
    public function dateString($key)
    {
        return $this->date($key)->format('r');
    }
}
