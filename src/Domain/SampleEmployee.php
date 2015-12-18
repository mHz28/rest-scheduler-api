<?php
/**
 * returns sample employee token
 */
namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Adr\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Project\Utils\Constants;
use Spark\Project\Utils\Utilities;

class SampleEmployee implements DomainInterface
{

    /**
     *
     * @var String
     * @var PayloadInterface
     */
    private $payload;

    private $token;

    /**
     *
     * @param ServerInterface $server            
     * @param PayloadInterface $payload            
     */
    public function __construct(ServerRequestInterface $server, PayloadInterface $payload)
    {
        $this->payload = $payload;
        
        $this->token = Utilities::getInstance()->generateToken($server, Constants::SAMPLE_EMPLOYEE_ID, Constants::EMPLOYEE);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        return $this->payload->withStatus(PayloadInterface::OK)->withOutput(array("token"=>
            $this->token
        ));
    }
}
