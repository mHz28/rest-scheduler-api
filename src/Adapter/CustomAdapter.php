<?php
/**
 * Custom Adapter to validate credentials or token
 */
namespace Spark\Project\Adapter;

use Spark\Auth\AdapterInterface as Adapter;
use Spark\Auth\Jwt\GeneratorInterface as Generator;
use Spark\Auth\Jwt\ParserInterface as Parser;
use Spark\Auth\Credentials;
use Spark\Project\Utils\Constants;

class CustomAdapter implements Adapter
{

    protected $generator;

    protected $parser;

    public function __construct(Generator $generator, Parser $parser)
    {
        $this->generator = $generator;
        
        $this->parser = $parser;
    }

    public function validateToken($token)
    {
        $parsed = $this->parser->parse((string) $token);
        
        // $parsed is an instance of \Spark\Auth\Token. You can call its
        // getMetadata() method here to get all metadata associated with the
        // token, such as a unique identifier for the user, in order to
        // validate the token.
        
        return $parsed;
    }

    public function validateCredentials(Credentials $credentials)
    {
        // Validate $credentials here, then assign to $claims an array
        // containing the JWT claims to associate with the generated token.
        
        // EKW: Successful validation assumed
        $tokenId = base64_encode(mcrypt_create_iv(32));
        
        $issuedAt = time();
        
        $notBefore = $issuedAt + 10; // Adding 10 seconds
        
        $expire = $notBefore + 864000; // Adding 30 days for example
        
        $serverName = gethostname(); // Retrieve the server name from config file
        
        $data = array(
            
            Constants::USER_ID => $credentials->getUserId(),
            
            Constants::ROLE => $credentials->getRole()
        )
        ;
        
        $claims = array(
            
            'iat' => $issuedAt, // Issued at: time when the token was generated
            
            'jti' => $tokenId, // Json Token Id: an unique identifier for the token
            
            'iss' => $serverName, // Issuer
            
            'nbf' => $notBefore, // Not before
            
            'exp' => $expire, // Expire
            
            'data' => $data
        )
        ;
        
        return $this->generator->getToken($claims);
    }
}