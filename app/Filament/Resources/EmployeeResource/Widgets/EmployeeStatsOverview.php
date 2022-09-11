<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Department;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Database\Eloquent\Builder;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {

        return [
            Card::make('All Employees', Employee::count()),
            Card::make("Backend Employees",$this->getEmployeesCountByDepartmentName('backend')),
            Card::make("Frontend Employees",$this->getEmployeesCountByDepartmentName('front end')),
            Card::make("Dev Ops Employees",$this->getEmployeesCountByDepartmentName('dev ops')),
        ];
    }

    private function getEmployeesCountByDepartmentName(string $name): int
    {
        return Employee::whereRelation('department', 'name', '=', $name)->count();
    }
}
