<?php
/**
 * User Story: As a manager, I want to be able to assign a shift, by changing the employee that will work a shift.
 */
namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Adr\PayloadInterface;
use Spark\Project\Utils\Constants;
use Spark\Project\Utils\Utilities;
use Spark\Project\Utils\Checker;
use Psr\Http\Message\ServerRequestInterface;

class EmployeeShiftUpdate extends Checker implements DomainInterface
{

    /**
     *
     * @var String
     * @var PayloadInterface
     */
    private $payload;

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
        
        $hasAuthorization = self::hasAuthorization($serverParams, $this->payload, Constants::MANAGER_ACCESS);
        
        if (! $hasAuthorization) {
            
            return self::getErrorStatus($this->payload);
        }
        
        $updateStatus = array(
            self::setDefaultMessage(Constants::SHIFT_ID . Constants::ANDSTR . Constants::EMPLOYEE_ID . Constants::ERROR_REQUIRED)
        );
        
        if (! empty($input[Constants::SHIFT_ID]) && ! empty($input[Constants::EMPLOYEE_ID])) {
            
            $shiftId = $input[Constants::SHIFT_ID];
            
            $employeeId = $input[Constants::EMPLOYEE_ID];
            
            $updateStatus = Utilities::getInstance()->updateEmployeeShift($shiftId, $employeeId); // dummy function: Results from DB implementation
            
            $status = PayloadInterface::OK;
        }
        
        return $this->payload->withStatus($status)->withOutput($updateStatus);
    }
}