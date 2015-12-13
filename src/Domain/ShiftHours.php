<?php
/**
 * Use Case :  As an employee, I want to know how much I worked, by being able to get a summary of hours worked for each week.
 */
namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Adr\PayloadInterface;
use Spark\Project\Model;
use \Spark\Project\Utils\Utilities;
use \Spark\Project\Utils\Constants;
use \Spark\Project\Utils\Checker;
use Psr\Http\Message\ServerRequestInterface;

class ShiftHours extends Checker implements DomainInterface
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
        $serverParams = $this->server->getServerParams();
        
        if (! self::hasAuthorization($serverParams, $this->payload)) {
            
            return self::getErrorStatus($this->payload);
        
        }
        
        $weekDate = date(Constants::DEFAULT_DATEFORMAT);
        
        if (isset($input[Constants::WEEK_DATE])) {
            
            $weekDate = $input[Constants::WEEK_DATE];
        }
        
        $token = Utilities::getInstance()->parseToken($serverParams);
        
        $data = Utilities::getInstance()->extractData($token);
        
        $employeeId = $data->user_id;
        
        $hours = array();
        
        if (! empty($employeeId)) {
            
            $hours = Utilities::getInstance()->findWeekHours($employeeId, $weekDate); // dummy function: Results from DB implementation
        }
        
        return $this->payload->withStatus(PayloadInterface::OK)->withOutput($hours);
    }
}
