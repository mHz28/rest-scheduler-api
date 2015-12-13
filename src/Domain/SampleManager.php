<?php
/**
 * returns sample manager token
 */
namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Adr\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Project\Utils\Constants;
use Spark\Project\Utils\Utilities;

class SampleManager implements DomainInterface
{

    /**
     *
     * @var PayloadInterface
     */
    private $payload;

    /**
     *
     * @var String
     */
    private $token;

    /**
     *
     * @param ServerInterface $server            
     * @param PayloadInterface $payload            
     */
    public function __construct(ServerRequestInterface $server, PayloadInterface $payload)
    {
        $this->payload = $payload;
        
        $this->token = Utilities::getInstance()->generateToken($server, Constants::SAMPLE_MANAGER_ID, Constants::MANAGER);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        return $this->payload->withStatus(PayloadInterface::OK)->withOutput(array(
            $this->token
        ));
    }
}
