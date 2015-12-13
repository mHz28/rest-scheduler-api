<?php
/**
 * Use Case : As an employee, I want to be able to contact my managers, by seeing manager contact information for my shifts.
 */
namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Adr\PayloadInterface;
use Spark\Project\Model;
use \Spark\Project\Utils\Utilities;
use \Spark\Project\Utils\Constants;
use \Spark\Project\Utils\Checker;
use Psr\Http\Message\ServerRequestInterface;

class EmployeeManager extends Checker implements DomainInterface
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
        
        $token = Utilities::getInstance()->parseToken($serverParams);
        
        $data = Utilities::getInstance()->extractData($token);
        
        $employeeId = $data->user_id;
        
        $shiftId = 0;
        
        if (isset($input[Constants::SHIFT_ID])) {
            
            $shiftId = $input[Constants::SHIFT_ID];
        }
        
        $shifts = array(self::setDefaultMessage(Constants::ERROR_SHIFTID_REQUIRED));
        
        if (! empty($shiftId)) {
            
            $shifts = Utilities::getInstance()->findEmployeeManager($shiftId); // Results from DB implementation, sample provided here with random generated data
            
            $status = PayloadInterface::OK;
        }
        
        return $this->payload->withStatus($status)->withOutput($shifts);
    }
}
