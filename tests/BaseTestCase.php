<?php

namespace App\Test;

use App\Entity\Employee;
use App\Service\Config;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    /** @var EntityManager */
    protected $em;
    /** @var ObjectRepository */
    protected $employee_repo;
    /** @var array[] */
    protected $employees = [
        [
            'name' => 'Alice',
            'age' => 26,
            'kids_num' => 2,
            'salary' => 6000,
            'company_car' => false,
            'calculated_salary' => 4800
        ],
        [
            'name' => 'Bob',
            'age' => 52,
            'kids_num' => 0,
            'salary' => 4000,
            'company_car' => true,
            'calculated_salary' => 2924
        ],
        [
            'name' => 'Charlie',
            'age' => 36,
            'kids_num' => 3,
            'salary' => 5000,
            'company_car' => true,
            'calculated_salary' => 3600
        ]
    ];

    /** @var Employee[] */
    protected $employee_entities = [];

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    protected function setUp()
    {
        $dbParams = [
            'driver' => Config::get('db_driver'),
            'user' => Config::get('db_user'),
            'password' => Config::get('db_password'),
            'dbname' => Config::get('db_name')
        ];

        $dbConfig = Setup::createAnnotationMetadataConfiguration(['src/Entity']);
        $dbConfig->addEntityNamespace('', 'App\Entity');
        $this->em = EntityManager::create($dbParams, $dbConfig);
        $this->employee_repo = $this->em->getRepository('App\Entity\Employee');

        foreach ($this->employees as $key => $value) {
            $birth_date = new DateTime();
            $birth_date->modify('-' . $value['age'] . ' year');
            $employee = (new Employee())
                ->setName($value['name'])
                ->setBirthDate($birth_date)
                ->setCompanyCar($value['company_car'])
                ->setKidsNum($value['kids_num'])
                ->setSalary($value['salary'])
            ;
            $this->em->persist($employee);
            $this->em->flush();
            $this->employee_entities[] = $employee;
            $this->employees[$key]['employee_id'] = $employee->getId();
            $this->employees[$key]['birth_date'] = $employee->getBirthDate()->format('Y-m-d');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        foreach ($this->employee_entities as $employee) {
            $this->em->remove($employee);
        }
        $this->em->flush();
    }
}