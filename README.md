Description
===
The project made with MVC pattern using controllers, entities, views and services. The tests made with using **PHPUnit**. **Composer** is used for autoload and for installing **PHPUnit** and **Doctrine**.

The simple frontend made with using **JQuery** and **Bootstrap**.

Installation
===
The project works with **php7.1**+ and **MySql5.6**, the database dump is in the project root. The DB connection parameters must be specified in the **/config.json** file.

The folder **/public/** in the project root must be specified as a site root directory, the local site address also must be specified in the **/config.json** - it's necessary for functional tests.

Also the command **composer install** must be done in the project root for installing **PHPUnit** and **Doctrine** and generating autoload files.

Logic
===
On the **http://{Domain_name}/** page user can watch, edit and delete existing employees and add new.

All communication with server made by using AJAX requests to the following routes:
---
- **/api/employee/list** - getting all employees list
- **/api/employee/add** - adding new employee
- **/api/employee/edit** - editing an employee
- **/api/employee/remove** - removing an employee

Testing
===
The project contains functional and Unit test, for running all the tests run **php vendor/phpunit/phpunit/phpunit** command in the project root.

Project structure
===
- **public/** - the site root containing **index.php** - the entry point with all routes definition
- **src/Controller/AbstractController.php** - parent controller with common controller logic
- **src/Controller/DefaultController.php** - controller for displaying index page
- **src/Controller/Api/EmployeeController.php** - controller for processing all actions with employees
- **src/Entity/JsonSerializable.php** - abstract serializable entity
- **src/Entity/Employee.php** - employee entity
- **src/Exception/** - exception classes thrown in the project
- **src/Service/EmployeeService.php** - the service for working with employees
- **src/Service/Config.php** - service for working with the project configuration
- **src/Service/FormatsChecker.php** - service for checking data formats
- **src/view/employees_page.tpl** - frontend
- **tests/BaseTestCase.php** - parent test class with common logic
- **tests/EmployeeControllerTest.php** - functional tests for testing EmployeeController imitating user actions
- **tests/EmployeeServiceTest.php** - Unit tests for testing EmployeeService methods
- **config.json** - config file
- **realforce.sql** - DB dump