<?php

require_once '../vendor/autoload.php';

use App\Controller\Api\EmployeeController;
use App\Controller\DefaultController;
use App\Service\Config;
use App\Service\EmployeeService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

$dbParams = [
    'driver' => Config::get('db_driver'),
    'user' => Config::get('db_user'),
    'password' => Config::get('db_password'),
    'dbname' => Config::get('db_name')
];

$dbConfig = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/../src/Entity']);
$dbConfig->addEntityNamespace('', 'App\Entity');
$em = EntityManager::create($dbParams, $dbConfig);

switch ($_GET['route']) {
    case '':
        $controller = new DefaultController();
        $controller->showEmployeesPage();
        break;
    case 'api/employee/list':
        $controller = new EmployeeController();
        $controller->getEmployeesList(new EmployeeService($em));
        break;
    case 'api/employee/add':
        $controller = new EmployeeController();
        $controller->addEmployee(file_get_contents('php://input'), new EmployeeService($em), $em);
        break;
    case 'api/employee/edit':
        $controller = new EmployeeController();
        $controller->editEmployee(file_get_contents('php://input'), new EmployeeService($em), $em);
        break;
    case 'api/employee/remove':
        $controller = new EmployeeController();
        $controller->removeEmployee(file_get_contents('php://input'), new EmployeeService($em), $em);
}