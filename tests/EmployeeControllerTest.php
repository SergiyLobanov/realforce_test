<?php

namespace App\Test;

use App\Exception\IncorrectRequestException;
use App\Exception\NotFoundException;
use App\Service\Config;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class EmployeeControllerTest extends BaseTestCase
{
    /** @var string $site_address */
    private $site_address;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->site_address = Config::get("site_address");

        parent::setUp();
    }

    public function testGetEmployeesList()
    {
        $result = $this->makeCurlRequest('/api/employee/list');

        $this->assertEquals(200, $result['code']);

        $this->assertEquals($this->employees[0], $result['content']['data'][$this->employees[0]['employee_id']]);

        $this->assertEquals($this->employees[1], $result['content']['data'][$this->employees[1]['employee_id']]);

        $this->assertEquals($this->employees[2], $result['content']['data'][$this->employees[2]['employee_id']]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testAddEmployee()
    {
        $path = '/api/employee/add';

        $exception = new IncorrectRequestException();

        //Empty request
        $result = $this->makeCurlRequest($path);
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        //Empty json
        $result = $this->makeCurlRequest($path, '{}');
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        $employee = $this->employees[0];

        //Without employee name
        unset($employee['name']);
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        $employee['name'] = $this->employees[0]['name'];

        //Without kids number
        unset($employee['kids_num']);
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        $employee['kids_num'] = $this->employees[0]['kids_num'];

        //Without company car parameter
        unset($employee['company_car']);
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        $employee['company_car'] = $this->employees[0]['company_car'];

        //Without salary
        unset($employee['salary']);
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        $employee['salary'] = $this->employees[0]['salary'];

        //Without birth date
        unset($employee['birth_date']);
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        //Incorrect birth date format
        $employee['birth_date'] = '123';
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        $employee['birth_date'] = $this->employees[0]['birth_date'];

        //Successful creation
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(200, $result['code']);
        $employee['employee_id'] = $result['content']['data']['employee_id'];
        $this->assertEquals($employee, $result['content']['data']);

        //Getting employee entity from the DB
        $employeeEntity = $this->employee_repo->find($employee['employee_id']);
        $this->assertNotEmpty($employeeEntity);
        $this->assertEquals($employee['name'], $employeeEntity->getName());

        //Removing employee
        $this->em->remove($employeeEntity);
        $this->em->flush();
    }

    public function testEditEmployee()
    {
        $path = '/api/employee/edit';

        $exception = new IncorrectRequestException();

        //Empty request
        $result = $this->makeCurlRequest($path);
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        //Empty json
        $result = $this->makeCurlRequest($path, '{}');
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        $employee = $this->employees[0];
        $employee['name'] = 'John';
        $employee['company_car'] = true;
        $employee['calculated_salary'] = 4300;

        //Without employee id
        unset($employee['employee_id']);
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        //Incorrect employee id
        $exception = new NotFoundException();
        $employee['employee_id'] = -1;
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(404, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        $employee['employee_id'] = $this->employees[0]['employee_id'];

        //Successful update
        $result = $this->makeCurlRequest($path, json_encode($employee));
        $this->assertEquals(200, $result['code']);
        $this->assertEquals($employee, $result['content']['data']);

        //Getting updated employee from the list
        $result = $this->makeCurlRequest('/api/employee/list');
        $this->assertEquals($employee, $result['content']['data'][$employee['employee_id']]);
    }

    public function testRemoveEmployee()
    {
        $path = '/api/employee/remove';

        $exception = new IncorrectRequestException();

        //Empty request
        $result = $this->makeCurlRequest($path);
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        //Empty json
        $result = $this->makeCurlRequest($path, '{}');
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        //Without employee id
        $result = $this->makeCurlRequest($path);
        $this->assertEquals(400, $result['code']);
        $this->assertEquals($exception->getMessage(), $result['content']['error']);

        //Successful removal
        $result = $this->makeCurlRequest($path, json_encode(['employee_id' => $this->employees[0]['employee_id']]));
        $this->assertEquals(200, $result['code']);

        //Checking the list
        $result = $this->makeCurlRequest('/api/employee/list');
        $this->assertEmpty($result['content']['data'][$this->employees[0]['employee_id']]);
    }

    /**
     * @param string $path
     * @param string $data
     * @return array
     */
    private function makeCurlRequest(string $path, string $data = ''): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->site_address . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return [
            'content' => json_decode($result, true),
            'code' => $code
        ];
    }
}