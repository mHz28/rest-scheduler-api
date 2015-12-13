<?php
/**
 * User Model
 */
namespace Spark\Project\Model;

use Spark\Data\EntityInterface;
use Spark\Data\Traits\EntityTrait;
use Spark\Project\Utils\Constants;

class User implements EntityInterface
{
    use EntityTrait;

    private $id;

    private $name;

    private $role;

    private $email;

    private $phone;

    private $created_at;

    private $updated_at;

    private function types()
    {
        return [
            Constants::ID => 'int',
            Constants::NAME => 'string',
            Constants::ROLE => 'string',
            Constants::EMAIL => 'string',
            Constants::PHONE => 'string',
            Constants::CREATED_AT => 'date',
            Constants::UPDATED_AT => 'date'
        ];
    }
}