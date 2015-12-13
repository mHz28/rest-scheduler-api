<?php
/**
 * Use Case :  As an employee, I want to know when I am working, by being able to see all of the shifts assigned to me.
 */
namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Adr\PayloadInterface;
use Spark\Project\Model;
use \Spark\Project\Utils\Utilities;
use \Spark\Project\Utils\Constants;
use \Spark\Project\Utils\Checker;
use Psr\Http\Message\ServerRequestInterface;

class EmployeeShifts extends Checker implements DomainInterface
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
 
        if (! self::hasAuthorization($serverParams, $this->payload)) {
            
            return self::getErrorStatus($this->payload);
        
        }
        
        $data = Utilities::getInstance()->extractData($serverParams);
        
        $employeeId = $data->user_id; //defaults to own id
        
        if(isset($input[Constants::EMPLOYEE_ID])&&self::hasAuthorization($serverParams, $this->payload, Constants::MANAGER_ACCESS)){
            
            $employeeId = $input[Constants::EMPLOYEE_ID]; //only Managers can query employee IDs
        }
        
        $shifts = array(self::setDefaultMessage(Constants::EMPLOYEE_ID.Constants::ERROR_REQUIRED));
        
        if (! empty($employeeId)) {
            
            $shifts = Utilities::getInstance()->findShiftsByEmployeeId($employeeId); //Results from DB implementation, sample provided here with random generated data
        
            $status = PayloadInterface::OK;
            
        }
        
        return $this->payload->withStatus($status)->withOutput($shifts);
    }
}
