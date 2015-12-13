<?php
/**
 * Use Case :  As an employee, I want to know who I am working with, by being able to see the employees that are working 
 * during the same time period as me.
 */
namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Adr\PayloadInterface;
use Spark\Project\Model;
use \Spark\Project\Utils\Utilities;
use \Spark\Project\Utils\Constants;
use \Spark\Project\Utils\Checker;
use Psr\Http\Message\ServerRequestInterface;

class ShiftBuddy extends Checker implements DomainInterface
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
        
        $shiftId = 0;
        
        if (isset($input[Constants::SHIFT_ID])) 

        {
            
            $shiftId = $input[Constants::SHIFT_ID];
        }
        
        if (! self::hasAuthorization($serverParams, $this->payload)) {
            
            return self::getErrorStatus($this->payload);
        }
        
        $data = Utilities::getInstance()->extractData($serverParams);
        
        $employeeId = $data->user_id;
        
        $buddies = array(self::setDefaultMessage(Constants::EMPLOYEE_ID.Constants::ERROR_REQUIRED));
        
        if (! empty($employeeId)) {
            
            $buddies = Utilities::getInstance()->findShiftBuddy($employeeId, $shiftId); // dummy function: Results from DB implementation
            
            $status = PayloadInterface::OK;
        }
        
        return $this->payload->withStatus($status)->withOutput($buddies);
    }
}
