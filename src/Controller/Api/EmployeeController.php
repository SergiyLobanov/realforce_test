<?php

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Exception\IncorrectRequestException;
use App\Service\EmployeeService;
use Doctrine\ORM\EntityManager;
use Exception;

class EmployeeController extends AbstractController
{
    /**
     * @param EmployeeService $employee_service
     */
    public function getEmployeesList(EmployeeService $employee_service): void
    {
        try
        {
            $this->json(null, [
                'data' => $employee_service->getEmployeesList()
            ]);
        }
        catch (Exception $e)
        {
            $this->json($e);
        }
    }

    /**
     * @param string $request
     * @param EmployeeService $employee_service
     * @param EntityManager $em
     */
    public function addEmployee(string $request, EmployeeService $employee_service, EntityManager $em): void
    {
        try
        {
            $json = json_decode($request, true);

            if (!is_array($json)) {
                throw new IncorrectRequestException();
            }

            $employee = $employee_service->addEmployee($json);

            $em->flush();

            $this->json(null, [
                'data' => $employee
            ]);
        }
        catch (Exception $e)
        {
            $this->json($e);
        }
    }

    /**
     * @param string $request
     * @param EmployeeService $employee_service
     * @param EntityManager $em
     */
    public function editEmployee(string $request, EmployeeService $employee_service, EntityManager $em): void
    {
        try
        {
            $json = json_decode($request, true);

            if (!is_array($json)) {
                throw new IncorrectRequestException();
            }

            $employee = $employee_service->editEmployee($json);

            $em->flush();

            $this->json(null, [
                'data' => $employee
            ]);
        }
        catch (Exception $e)
        {
            $this->json($e);
        }
    }

    /**
     * @param string $request
     * @param EmployeeService $employee_service
     * @param EntityManager $em
     */
    public function removeEmployee(string $request, EmployeeService $employee_service, EntityManager $em): void
    {
        try
        {
            $json = json_decode($request, true);

            if (empty($json['employee_id'])) {
                throw new IncorrectRequestException();
            }

            $employee_service->removeEmployee((int)$json['employee_id']);

            $em->flush();

            $this->json(null);
        }
        catch (Exception $e)
        {
            $this->json($e);
        }
    }
}