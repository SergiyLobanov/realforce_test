<?php

namespace App\Controller;

class DefaultController extends AbstractController
{
    public function showEmployeesPage(): void
    {
        $this->show('employees_page');
    }
}