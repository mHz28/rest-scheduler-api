<?php
/**
 * User Story: As a manager, I want to contact an employee, by seeing employee details.
 */
namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Adr\PayloadInterface;
use Spark\Project\Utils\Constants;
use Spark\Project\Utils\Utilities;
use Spark\Project\Utils\Checker;
use Psr\Http\Message\ServerRequestInterface;

class Employee extends Checker implements DomainInterface
{

    /**
     * @var String
     * @var PayloadInterface
     */
    private $payload;

    private $server;

    /**
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
        
        $employeeDetail = array(self::setDefaultMessage(Constants::EMPLOYEE_ID.Constants::ERROR_REQUIRED));
        
        if (! empty($input[Constants::EMPLOYEE_ID])) {
            
            $employeeId = $input[Constants::EMPLOYEE_ID];
            
            $employeeDetail = Utilities::getInstance()->findEmployeeById($employeeId);
            
            $status = PayloadInterface::OK;
            
        }   
        
        return $this->payload->withStatus($status)->withOutput($employeeDetail);
        
    }
}