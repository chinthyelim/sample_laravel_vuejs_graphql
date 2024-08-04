<?php

namespace App\Models;

use App\Models\Base\Employee as BaseEmployee;
class Employee extends BaseEmployee
{
    public function scopePublic($query)
    {
        return $query->leftJoin('companies', 'employees.company_id', '=', 'companies.id')->select('employees.*', 'companies.name', 'companies.logo');
    }
}
