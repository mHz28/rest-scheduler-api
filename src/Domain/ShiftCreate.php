<?php
/**
 * User Story :  As a manager, I want to schedule my employees, by creating shifts for any employee.
 */
namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Adr\PayloadInterface;
use Spark\Project\Model;
use \Spark\Project\Utils\Utilities;
use \Spark\Project\Utils\Constants;
use \Spark\Project\Utils\Checker;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Project\Model\Shift;

class ShiftCreate extends Checker implements DomainInterface
{

    /**
     *
     * @var PayloadInterface
     */
    private $payload;

    /**
     *
     * @var ServerInterface
     */
    private $server;

    /**
     *
     * @param ServerInterface $server            
     * @param PayloadInterface $payload            
     */
    public function __construct(ServerRequestInterface $server, PayloadInterface $payload)
    {
        $this->payload = $payload;
        
        $this->server = $server;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $status = PayloadInterface::INVALID;
        
        $serverParams = $this->server->getServerParams();
        
        if (! self::hasAuthorization($serverParams, $this->payload, Constants::MANAGER_ACCESS)) {
            
            return self::getErrorStatus($this->payload);
        }
        
        $data = Utilities::getInstance()->extractData($serverParams);
        
        $managerId = $data->user_id;
        
        $insertStatus = array(
            
            self::setDefaultMessage(Constants::EMPLOYEE_ID . Constants::ANDSTR . Constants::BREAKS . Constants::ANDSTR . Constants::FROM_DATE . Constants::ANDSTR . Constants::TO_DATE . Constants::ANDSTR)
        )
        ;
        
        if (self::hasRequiredParams($input)) {
            
            $employeeId = $input[Constants::EMPLOYEE_ID];
            
            $breaks = $input[Constants::BREAKS];
            
            $startTime = $input[Constants::FROM_DATE];
            
            $endTime = $input[Constants::TO_DATE];
            
            $now = date(Constants::DEFAULT_DATEFORMAT);
            
            $newShift = new Shift([
                
                Constants::MANAGER_ID => $managerId,
                Constants::EMPLOYEE_ID => $employeeId,
                Constants::BREAKS => $breaks,
                Constants::START_TIME => $startTime,
                Constants::END_TIME => $endTime,
                Constants::CREATED_AT => $now,
                Constants::UPDATED_AT => $now
            ]
            )

            ;
            
            $insertStatus = Utilities::getInstance()->addShift($newShift); // dummy function: Results from DB implementation
            
            $status = PayloadInterface::OK;
        }
        
        return $this->payload->withStatus($status)->withOutput($insertStatus);
    }

    private function hasRequiredParams($input)
    {
        if (isset($input[Constants::EMPLOYEE_ID]) && isset($input[Constants::BREAKS]) && isset($input[Constants::FROM_DATE]) && isset($input[Constants::TO_DATE])) 

        {
            
            return true;
        }
        
        return false;
    }
}
