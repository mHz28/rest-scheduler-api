<?php
/**
 * Sample Singleton for Demo Data Purposes. Test Environment only.
 */
namespace Spark\Project\Utils;

use \Spark\Project\Model\User;
use Spark\Project\Adapter\CustomAdapter;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Auth\Credentials;
use Spark\Auth\Jwt\Configuration;
use Spark\Auth\Jwt\FirebaseGenerator;
use Spark\Auth\Jwt\FirebaseParser;
use Spark\Auth\Credentials\ExtractorInterface;
use Spark\Auth\AdapterInterface;
use Spark\Project\Model\Shift;
require '../vendor/fzaninotto/faker/src/autoload.php';

class Utilities
{

    private static $instance;

    private $employees = array();

    private $shifts = array();

    private $configuration;

    private function __construct()
    {
        self::buildEmployeeList();
        $this->configuration = self::setConfiguration();
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function setConfiguration()
    {
        $issuedAt = time();
        $notBefore = $issuedAt + 10;
        $expire = $notBefore + 864000;
        
        return new Configuration(Constants::PUBLIC_KEY, $expire, Constants::ALGORITHM);
    }

    public function getAllEmployees()
    {
        return array(
            $this->employees
        );
    }

    public function findEmployeeManager($shiftId)
    {
        $shift = self::findShiftById($shiftId);
        
        $manager = array();
        
        $managerId = $shift[0]->manager_id;
        
        if (count($shift) > 0) {
            $manager = array(
                self::findEmployeeById($managerId)
            );
        }
        
        return $manager;
    }

    public function findShiftById($shiftId)
    {
        $shifts = array();
        
        foreach ($this->shifts as $shift) {
            
            $id = $shift->id;
            
            if ($id == $shiftId) {
                $shifts[] = $shift;
            }
        }
        
        return $shifts;
    }

    public function findShiftsByEmployeeId($employeeId)
    {
        $shifts = array();
        
        foreach ($this->shifts as $shift) {
            
            $shiftEmployeeId = $shift->employee_id;
            
            if ($employeeId == $shiftEmployeeId) {
                $shifts[] = $shift;
            }
        }
        
        return $shifts;
    }

    public function findEmployeeById($id)
    {
        $resultEmployee = Constants::EMPTYSTR;
        
        foreach ($this->employees as $employee) {
            
            $employeeId = $employee->id;
            
            if ($id == $employeeId) {
                $resultEmployee = $employee;
            }
        }
        
        return array(
            $resultEmployee
        );
    }

    private function buildEmployeeList()
    {
        $userList = array(
            array(
                Constants::ROLE => Constants::MANAGER,
                Constants::MAXUSERS => Constants::MAX_MANAGERS
            ),
            array(
                Constants::ROLE => Constants::EMPLOYEE,
                Constants::MAXUSERS => Constants::MAX_EMPLOYEES
            )
        );
        
        $autoincrement = 0;
        
        foreach ($userList as $userParameter) {
            $role = $userParameter[Constants::ROLE];
            $maxUsers = $userParameter[Constants::MAXUSERS];
            
            for ($x = 0; $x < $maxUsers; ++ $x) {
                
                ++ $autoincrement;
                
                $this->employees[] = self::createEmployee($autoincrement, $role);
                
                self::createShifts($autoincrement);
            }
        }
    }

    private function createShifts($userId)
    {
        $sampleShifts = rand(10, 20);
        
        for ($x = 0; $x < $sampleShifts; ++ $x) {
            
            $this->shifts[] = self::generateShift($userId);
        }
    }

    private function generateShift($userId)
    {
        $startDateRange = date(Constants::DEFAULT_DATEFORMAT);
        $endDay = date('t', strtotime($startDateRange));
        $endDateRange = date("Y-m-$endDay H:i:s");
        
        $id = count($this->shifts) + 1;
        $hoursWorked = rand(1, 12);
        $breaks = rand(1, 3);
        
        $startDate = self::randTime(date(Constants::DEFAULT_DATEFORMAT, strtotime($endDateRange)), date(Constants::DEFAULT_DATEFORMAT, strtotime($startDateRange)));
        
        $endDate = date(Constants::DEFAULT_DATEFORMAT, strtotime("+$hoursWorked hour", strtotime($startDate)));
        
        return new Shift([
            Constants::ID => $id,
            Constants::MANAGER_ID => Constants::SAMPLE_MANAGER_ID,
            Constants::EMPLOYEE_ID => $userId,
            Constants::BREAKS => $breaks,
            Constants::START_TIME => $startDate,
            Constants::END_TIME => $endDate,
            Constants::CREATED_AT => self::getCurrentDate(),
            Constants::UPDATED_AT => self::getCurrentDate()
        ]);
    }

    private function getCurrentDate()
    {
        return date(Constants::DEFAULT_DATEFORMAT);
    }

    private function randTime($max, $min)
    {
        return date(Constants::DEFAULT_DATEFORMAT, mt_rand(strtotime($min), strtotime($max)));
    }

    private function createEmployee($id, $role)
    {
        $faker = \Faker\Factory::create();
        
        return new User([
            Constants::ID => $id,
            Constants::NAME => $faker->name,
            Constants::ROLE => $role,
            Constants::EMAIL => $faker->email,
            Constants::PHONE => $faker->phoneNumber,
            Constants::CREATED_AT => self::getCurrentDate(),
            Constants::UPDATED_AT => self::getCurrentDate()
        ]
        );
    }

    public function generateToken(ServerRequestInterface $server, $sampleUserId, $sampleRole)
    {
        $credentials = new Credentials(Constants::USERNAME, Constants::PASSWORD, $sampleUserId, $sampleRole);
        
        return self::getCustomAdapter($server)->validateCredentials($credentials);
    }

    public function parseToken($serverParams)
    {
        $token = $serverParams["HTTP_AUTHORIZATION"];
        
        $parser = new FirebaseParser($this->configuration);
        
        return $parser->parseToken($token);
    }

    public function extractData($serverParams)
    {
        $token = Utilities::getInstance()->parseToken($serverParams);
        
        if ($token->getMetadata(Constants::DATA) != Constants::EMPTYSTR) {
            
            return $token->getMetadata(Constants::DATA);
        }
        
        return array();
    }

    public function getCustomAdapter($server)
    {
        $firebase = new FirebaseGenerator($server, $this->configuration);
        $parser = new FirebaseParser($this->configuration);
        
        return new CustomAdapter($firebase, $parser);
    }
    
    /*
     * Dummy Functions below to mimic db calls
     */
    public function findShiftBuddy($employeeId, $shiftId)
    {
        /*
         * returns array of Users from db
         */
        return array(
            "results From DB"
        );
    }

    public function findWeekHours($employeeId, $weekDate)
    {
        /*
         * returns array of Users from db
         */
        return array();
    }

    public function findShiftsByRange($startTime, $endTime)
    {
        /*
         * returns array of Shifts from db
         */
        return array();
    }

    public function updateShift($shiftId, $startTime, $endTime)
    {
        /*
         * returns status of update
         */
        return array();
    }

    public function updateEmployeeShift($shiftId, $employeeId)
    {
        /*
         * returns status of update
         */
        return array();
    }

    public function addShift($shift)
    {
        /*
         * returns status of insert
         */
        return array();
    }
}