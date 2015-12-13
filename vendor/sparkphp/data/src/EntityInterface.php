<?php

namespace Spark\Data;

interface EntityInterface extends
    ArraySerializableInterface,
    \JsonSerializable,
    \Serializable
{
    /**
     * Check if a property exists in this entity
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Get a copy of the entity and replace data
     *
     * @param array $data
     *
     * @return static
     */
    public function withData(array $data);
}
