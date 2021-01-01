<?php

namespace App\Entity;

use DateTime;
use Exception;

/**
 * Class Employee
 * @package App\Entity
 *
 * @Entity
 * @Table(name="employees")
 */
class Employee extends JsonSerializable
{
    //Country Tax for salaries is 20%
    public const TAX = 20;

    /**
     * @var int $id
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @var string $name
     *
     * @Column(type="string", length=255)
     */
    private $name;

    /**
     * @var DateTime $birth_date
     *
     * @Column(type="date")
     */
    private $birth_date;

    /**
     * @var int $kids_num
     *
     * @Column(type="integer")
     */
    private $kids_num;

    /**
     * @var float $salary
     *
     * @Column(type="float")
     */
    private $salary;

    /**
     * @var bool $company_car
     *
     * @Column(type="boolean")
     */
    private $company_car;

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize(): array
    {
        return [
            'employee_id' => $this->id,
            'name' => $this->name,
            'age' => $this->getAge(),
            'birth_date' => $this->birth_date->format('Y-m-d'),
            'kids_num' => $this->kids_num,
            'salary' => $this->salary,
            'company_car' => $this->company_car,
            'calculated_salary' => $this->calculateSalary()
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return JsonSerializable
     */
    public function setId(int $id): JsonSerializable
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DateTime
     */
    public function getBirthDate(): DateTime
    {
        return $this->birth_date;
    }

    /**
     * @return int
     */
    public function getKidsNum(): int
    {
        return $this->kids_num;
    }

    /**
     * @return float
     */
    public function getSalary(): float
    {
        return $this->salary;
    }

    /**
     * @return bool
     */
    public function getCompanyCar(): bool
    {
        return $this->company_car;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getAge(): int
    {
        $now = new DateTime();

        $diff = $now->diff($this->birth_date);

        return $diff->y;
    }

    /**
     * @return float
     * @throws Exception
     */
    public function calculateSalary(): float
    {
        $salary = $this->salary;
        $decimals = 2;
        $tax = self::TAX;

        //If an employee older than 50 we add 7% to his salary
        if ($this->getAge() >= 50) {
            $salary = bcmul($salary, '1.07', $decimals);
        }
        //If an employee has more than 2 kids we decrease his Tax by 2%
        if ($this->kids_num > 2) {
            $tax -= 2;
        }
        //Counting the user's tax sum
        $tax_sum = bcdiv(bcmul($salary, $tax, $decimals), '100', $decimals);
        $salary = bcsub($salary, $tax_sum);
        //If an employee wants to use a company car we need to deduct $500
        if ($this->company_car) {
            $salary = bcsub($salary, '500', $decimals);
        }
        return (float)$salary;
    }

    /**
     * @param string $name
     * @return Employee
     */
    public function setName(string $name): Employee
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param DateTime $birth_date
     * @return Employee
     */
    public function setBirthDate(DateTime $birth_date): Employee
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    /**
     * @param int $kids_num
     * @return Employee
     */
    public function setKidsNum(int $kids_num): Employee
    {
        $this->kids_num = $kids_num;

        return $this;
    }

    /**
     * @param float $salary
     * @return Employee
     */
    public function setSalary(float $salary): Employee
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * @param bool $company_car
     * @return Employee
     */
    public function setCompanyCar(bool $company_car): Employee
    {
        $this->company_car = $company_car;

        return $this;
    }
}