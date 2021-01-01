<?php

namespace App\Service;

use App\Entity\Employee;
use App\Exception\IncorrectRequestException;
use App\Exception\NotFoundException;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectRepository;
use Exception;

class EmployeeService
{
    /** @var EntityManager */
    private $em;

    /** @var ObjectRepository */
    private $employee_repo;

    /**
     * EmployeeService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->employee_repo = $em->getRepository('App\Entity\Employee');
    }

    /**
     * @return Employee[]
     */
    public function getEmployeesList(): array
    {
        return $this->employee_repo->findAll();
    }

    /**
     * @param array $data
     * @return Employee
     * @throws Exception
     */
    public function addEmployee(array $data): Employee
    {
        $employee = new Employee();

        $this->setData($employee, $data);

        $this->em->persist($employee);

        return $employee;
    }

    /**
     * @param array $data
     * @return Employee
     * @throws IncorrectRequestException
     * @throws NotFoundException
     * @throws Exception
     */
    public function editEmployee(array $data): Employee
    {
        if (empty($data['employee_id'])) {
            throw new IncorrectRequestException();
        }

        /** @var Employee $employee */
        $employee = $this->employee_repo->find((int)$data['employee_id']);

        if (!$employee) {
            throw new NotFoundException();
        }

        $this->setData($employee, $data);

        return $employee;
    }

    /**
     * @param int $employee_id
     * @throws NotFoundException
     * @throws ORMException
     */
    public function removeEmployee(int $employee_id): void
    {
        /** @var Employee $employee */
        $employee = $this->employee_repo->find((int)$employee_id);

        if (!$employee) {
            throw new NotFoundException();
        }

        $this->em->remove($employee);
    }

    /**
     * @param Employee $employee
     * @param array $data
     * @throws Exception
     */
    private function setData(Employee $employee, array $data): void
    {
        if (
            empty($data['name']) || empty($data['birth_date']) || !isset($data['kids_num']) ||
            !isset($data['salary']) || !isset($data['company_car']) ||
            !FormatsChecker::checkDate($data['birth_date'])
        ) {
            throw new IncorrectRequestException();
        }

        $employee->setName($data['name'])
            ->setBirthDate(new DateTime($data['birth_date']))
            ->setKidsNum((int)$data['kids_num'])
            ->setCompanyCar((bool)$data['company_car'])
            ->setSalary((float)$data['salary'])
        ;
    }
}