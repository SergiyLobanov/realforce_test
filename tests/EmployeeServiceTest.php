<?php

namespace App\Test;

use App\Exception\IncorrectRequestException;
use App\Exception\NotFoundException;
use App\Service\EmployeeService;
use Doctrine\ORM\ORMException;
use Exception;

class EmployeeServiceTest extends BaseTestCase
{
    /** @var EmployeeService $es */
    private $es;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->es = new EmployeeService($this->em);
    }

    /**
     * @throws Exception
     */
    public function testGetEmployeesList()
    {
        $exception = null;

        try
        {
            $employees = $this->es->getEmployeesList();
        }
        catch (Exception $e)
        {
            $exception = $e;
        }

        $this->assertEquals(null, $exception);

        $employee_founded = false;
        foreach ($employees as $employee) {
            if ($employee->getId() === $this->employee_entities[0]->getId()) {
                $this->assertEquals($employee->getName(), $this->employee_entities[0]->getName());
                $this->assertEquals($employee->getBirthDate(), $this->employee_entities[0]->getBirthDate());
                $this->assertEquals($employee->getCompanyCar(), $this->employee_entities[0]->getCompanyCar());
                $this->assertEquals($employee->getKidsNum(), $this->employee_entities[0]->getKidsNum());
                $this->assertEquals($employee->getSalary(), $this->employee_entities[0]->getSalary());
                $this->assertEquals($employee->calculateSalary(), $this->employee_entities[0]->calculateSalary());
                $employee_founded = true;
            }
        }

        $this->assertEquals(true, $employee_founded);
    }

    /**
     * @throws ORMException
     */
    public function testAddEmployee()
    {
        $employee = $this->employees[0];

        //Without employee name
        unset($employee['name']);
        $exception = null;
        try
        {
            $this->es->addEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(IncorrectRequestException::class, get_class($exception));
        $this->assertEquals(400, $exception->getCode());

        $employee['name'] = $this->employees[0]['name'];

        //Without kids number
        unset($employee['kids_num']);
        $exception = null;
        try
        {
            $this->es->addEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(IncorrectRequestException::class, get_class($exception));
        $this->assertEquals(400, $exception->getCode());

        $employee['kids_num'] = $this->employees[0]['kids_num'];

        //Without company car parameter
        unset($employee['company_car']);
        $exception = null;
        try
        {
            $this->es->addEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(IncorrectRequestException::class, get_class($exception));
        $this->assertEquals(400, $exception->getCode());

        $employee['company_car'] = $this->employees[0]['company_car'];

        //Without salary
        unset($employee['salary']);
        $exception = null;
        try
        {
            $this->es->addEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(IncorrectRequestException::class, get_class($exception));
        $this->assertEquals(400, $exception->getCode());

        $employee['salary'] = $this->employees[0]['salary'];

        //Without birth date
        unset($employee['birth_date']);
        $exception = null;
        try
        {
            $this->es->addEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(IncorrectRequestException::class, get_class($exception));
        $this->assertEquals(400, $exception->getCode());

        //Incorrect birth date format
        $employee['birth_date'] = '123';
        $exception = null;
        try
        {
            $this->es->addEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(IncorrectRequestException::class, get_class($exception));
        $this->assertEquals(400, $exception->getCode());

        $employee['birth_date'] = $this->employees[0]['birth_date'];

        //Successful creation
        $exception = null;
        try
        {
            $result = $this->es->addEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(null, $exception);
        $this->assertEquals($employee['name'], $result->getName());
        $this->assertEquals($employee['kids_num'], $result->getKidsNum());
        $this->assertEquals($employee['company_car'], $result->getCompanyCar());
        $this->assertEquals($employee['salary'], $result->getSalary());

        //Entity removing
        $this->em->remove($result);
    }

    /**
     * @throws Exception
     */
    public function testEditEmployee()
    {
        $employee = $this->employees[0];

        $employee['name'] = 'John';

        $employee['company_car'] = true;

        $employee['calculated_salary'] = 4300;

        //Without employee id
        unset($employee['employee_id']);
        $exception = null;
        try
        {
            $this->es->editEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(IncorrectRequestException::class, get_class($exception));
        $this->assertEquals(400, $exception->getCode());

        //Incorrect employee id
        $employee['employee_id'] = -1;
        $exception = null;
        try
        {
            $this->es->editEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(NotFoundException::class, get_class($exception));
        $this->assertEquals(404, $exception->getCode());

        //Successful update
        $employee['employee_id'] = $this->employees[0]['employee_id'];
        $exception = null;
        try
        {
            $result = $this->es->editEmployee($employee);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(null, $exception);
        $this->assertEquals($employee['name'], $result->getName());
        $this->assertEquals($employee['kids_num'], $result->getKidsNum());
        $this->assertEquals($employee['company_car'], $result->getCompanyCar());
        $this->assertEquals($employee['salary'], $result->getSalary());
        $this->assertEquals($employee['calculated_salary'], $result->calculateSalary());
    }

    public function testRemoveEmployee()
    {
        //Incorrect employee id
        $exception = null;
        try
        {
            $this->es->removeEmployee(-1);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(NotFoundException::class, get_class($exception));
        $this->assertEquals(404, $exception->getCode());

        //Successful removal
        $exception = null;
        try
        {
            $this->es->removeEmployee((int)$this->employees[0]['employee_id']);
        }
        catch (Exception $e)
        {
            $exception = $e;
        }
        $this->assertEquals(null, $exception);

        $this->em->flush();

        //Checking the DB
        $employee = $this->employee_repo->find((int)$this->employees[0]['employee_id']);
        $this->assertEmpty($employee);
    }
}