<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use PhpParser\Node\Expr\Cast\Array_;

abstract class ApiTestCase extends TestCase
{
    use DatabaseTransactions;

    protected $company;
    protected $employee;

    protected function setUp() :void
    {
        parent::setUp();

        $user = User::findOrFail(1);
        $this->actingAs($user);

        $this->company = new Company;
        $this->employee = new Employee;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->company = null;
        $this->employee = null;
    }

    protected function getCompanies(): Collection
    {
        $companies = $this->company::get();
        if (!count($companies)) {
            $this->company::factory()->create();
            $companies = $this->company::get();
        }
        return $companies;
    }

    protected function getNonExistCompany(): Array
    {
        $found = FALSE;
        $count = 0;
        $name = "";
        $email = "";
        $logo = "";
        $website = "";
        do {
            $name = fake()->unique()->name();
            $email = fake()->unique()->safeEmail();
            $logo = $this->createRandomWord(15) . ".png";
            $website = $this->createRandomWord(15);
            $company = $this->company::where([
                ['name', $name],
                ['email', $email],
                ['logo', $logo],
                ['website', $website],
            ])->get();
            if (!count($company)) {
                $found = TRUE;
            }
            ++$count;
        } while (!$found && $count <= 10);
        return array($found, $name, $email, $logo, $website);
    }
    protected function getEmployees(): Collection
    {
        $employees = $this->employee::get();
        if (!count($employees)) {
            $this->employee::factory()->create();
            $employees = $this->employee::get();
        }
        return $employees;
    }

    protected function getNonExistEmployee(): Array
    {
        $companies = $this->company::get();
        if (!count($companies)) {
            $companies = $this->getCompanies();
        }
        $found = FALSE;
        $count = 0;
        $first_name = "";
        $last_name = "";
        $company_id = $companies[0]->id;
        $email = "";
        $phone = "";
        do {
            $first_name = fake()->unique()->name();
            $last_name = fake()->unique()->name();
            $email = fake()->unique()->safeEmail();
            $phone = fake()->unique()->phoneNumber();
            $employee = $this->employee::where([
                ['first_name', $first_name],
                ['last_name', $last_name],
                ['company_id', $company_id],
                ['email', $email],
                ['phone', $phone],
            ])->get();
            if (!count($employee)) {
                $found = TRUE;
            }
            ++$count;
        } while (!$found && $count <= 10);
        return array($found, $company_id, $first_name, $last_name, $email, $phone);
    }

    protected function createRandomWord(int $strLength): string
    {
        return fake()->regexify('[A-Za-z0-9]{' . $strLength . '}');
    }

    protected function dummyBase64LogoImage(): string
    {
        return 'data:image/png;base64,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';
    }
}
