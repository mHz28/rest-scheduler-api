<?php

namespace Spark\Data\Traits;

trait EntityTrait
{
    use ImmutableValueObjectTrait;
    use DateAwareTrait;
    use JsonAwareTrait;
    use SerializeAwareTrait;
}
