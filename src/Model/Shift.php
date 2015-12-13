<?php
/**
 * Shift Model
 */
namespace Spark\Project\Model;

use Spark\Data\EntityInterface;
use Spark\Data\Traits\EntityTrait;
use Spark\Project\Utils\Constants;

class Shift implements EntityInterface
{
    use EntityTrait;

    private $id;

    private $manager_id;

    private $employee_id;

    private $break;

    private $start_time;

    private $end_time;

    private $created_at;

    private $updated_at;

    private function types()
    {
        return [
            Constants::ID => 'int',
            Constants::MANAGER_ID => 'int',
            Constants::EMPLOYEE_ID => 'int',
            Constants::BREAKS => 'float',
            Constants::START_TIME => 'date',
            Constants::END_TIME => 'date',
            Constants::CREATED_AT => 'date',
            Constants::UPDATED_AT => 'date'
        ];
    }
}