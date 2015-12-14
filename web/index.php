<?php

// Include Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

use Spark\Auth\Credentials\ExtractorInterface;
use Spark\Auth\Credentials\JsonExtractor;
use Spark\Project\Utils;
use Spark\Project\Utils\Constants;

// Configure the dependency injection container
$injector = new \Auryn\Injector();
$configuration = new \Spark\Configuration\DefaultConfigurationSet();
$configuration->apply($injector);

// Configure middleware

$injector->alias(AdapterInterface::class, Adapter::class);

$injector->alias(ExtractorInterface::class, JsonExtractor::class);
$injector->define(QueryExtractor::class, [
    ':parameter' => 'al'
]);

$injector->alias('\\Spark\\Middleware\\Collection', '\\Spark\\Middleware\\DefaultCollection');

// Configure the router
$injector->prepare('\\Spark\\Router', function (\Spark\Router $router)
{
    $router->get('/employee/sample', 'Spark\Project\Domain\SampleEmployee');
    $router->get('/manager/sample', 'Spark\Project\Domain\SampleManager');
    
    // As an employee, I want to know who I am working with, by being able to see the employees that are working during the same time period as me.
    $router->get('/employee/shift/buddy[/{' . Constants::SHIFT_ID . '}]', 'Spark\Project\Domain\ShiftBuddy');
    
    // As an employee, I want to know how much I worked, by being able to get a summary of hours worked for each week.
    $router->get('/employee/shift/hours[/{' . Constants::WEEK_DATE . '}]', 'Spark\Project\Domain\ShiftHours');
    
    // As an employee, I want to be able to contact my managers, by seeing manager contact information for my shifts.
    $router->get('/employee/manager[/{' . Constants::SHIFT_ID . '}]', 'Spark\Project\Domain\EmployeeManager');
    
    // As an employee, I want to know when I am working, by being able to see all of the shifts assigned to me
    $router->get('/employee/shifts', 'Spark\Project\Domain\EmployeeShifts');
    
    // As a manager, I want to schedule my employees, by creating shifts for any employee.
    $router->post('/manager/shift/{' . Constants::EMPLOYEE_ID . '}/{' . Constants::BREAKS . '}/{' . Constants::FROM_DATE . '}/{' . Constants::TO_DATE . '}', 'Spark\Project\Domain\ShiftCreate');
    
    // As a manager, I want to contact an employee, by seeing employee details.
    $router->get('/manager/employee_detail/{' . Constants::EMPLOYEE_ID . '}', 'Spark\Project\Domain\Employee');
    
    // As a manager, I want to see the schedule, by listing shifts within a specific time period.
    $router->get('/manager/shifts/{' . Constants::FROM_DATE . '}/{' . Constants::TO_DATE . '}', 'Spark\Project\Domain\ShiftRange');
    
    // As a manager, I want to be able to change a shift, by updating the time details.
    $router->put('/manager/shift/{' . Constants::SHIFT_ID . '}/{' . Constants::FROM_DATE . '}/{' . Constants::TO_DATE . '}', 'Spark\Project\Domain\ShiftUpdate');
    
    // As a manager, I want to be able to assign a shift, by changing the employee that will work a shift.
    $router->put('/manager/employee/shift/{' . Constants::SHIFT_ID . '}/{' . Constants::EMPLOYEE_ID . '}', 'Spark\Project\Domain\EmployeeShiftUpdate');
});

// Bootstrap the application
$dispatcher = $injector->make('\\Relay\\Relay');
$dispatcher($injector->make('Psr\\Http\\Message\\ServerRequestInterface'), $injector->make('Psr\\Http\\Message\\ResponseInterface'));
