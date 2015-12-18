# rest-scheduler-api

Sample project with Spark-Php, sparkphp/auth, firebase/php-jwt

Some sample data are generated to demostrate data structure. Utilities.php is sample worker Singleton that exist to provide sample data or as placeholder for functions leading to database implementation/

Get Started : Get sample tokens to be used in AUTHORIZATION header.

Emplyee token : [GET] /employee/sample

Manager token : [GET] /manager/sample

Current default data has 1 Manager and 1 Employee. Counts can be increased by modifying Constants::MAX_EMPLOYEES and Constants::MAX_MANAGERS.

to start Server: 
php -S localhost:8000 -t web/