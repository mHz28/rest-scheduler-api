<?php

namespace Spark\Data\Traits;

trait SerializeAwareTrait
{
    // Serializable
    public function serialize()
    {
        return serialize($this->toArray());
    }

    // Serializable
    public function unserialize($data)
    {
        $this->apply(unserialize($data));
    }
}
