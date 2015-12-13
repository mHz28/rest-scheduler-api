<?php
namespace Spark\Auth;

class Credentials
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var int
     */
    private $userId;
    
    /**
     * @var string
     */
    private $role;
    
    /**
     * @var string
     */
    private $password;

    /**
     * @param string $identifier
     * @param string $password
     */
    public function __construct($identifier, $password, $userId, $role)
    {
        $this->identifier = $identifier;
        $this->password = $password;
        $this->userId = $userId;
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * @return string
     */
    public function getUserId()
    {
    	return $this->userId;
    }
    /**
     * @return string
     */
    public function getRole()
    {
    	return $this->role;
    }
}
