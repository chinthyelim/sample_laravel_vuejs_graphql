<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class EmployeeTest extends ApiTestCase
{
    public function test_get_full_list_success(): void
    {
        $employees = $this->employee::all();
        $response = $this->getJson('/api/v1/employees');
        $response->assertStatus(200);
        $this->assertEquals($employees->count(), count($response->json()['data']), "actual value is equal to expected");
    }

    public function test_get_custom_list_success(): void
    {
        $paramString = http_build_query([ 'current_page_number' => 1, 'rows_per_page' => 2]);
        $response = $this->getJson("/api/v1/employees?$paramString");
        $response->assertStatus(200);
        $this->assertLessThanOrEqual(2, count($response->json()['data']), "actual value is Less than or equal to expected");
    }

    public function test_create_success(): void
    {
        list($found, $company_id, $first_name, $last_name, $email, $phone) = $this->getNonExistEmployee();
        if (!$found) {
            $this->assertTrue(FALSE);
            return;
        }
        $response = $this->postJson('/api/v1/employees', [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company_id' => $company_id,
            'email' => $email,
            'phone' => $phone,
        ]);
        $response->assertStatus(201)
                    ->assertJson(fn (AssertableJson $json) =>
                        $json->where('first_name', $first_name)
                            ->where('last_name', $last_name)
                            ->where('company_id', $company_id)
                            ->where('email', fn (string $employee_email) => str($employee_email)->is($email))
                            ->where('phone', $phone)
                            ->missingAll(['created_at', 'updated_at'])
                            ->etc()
                    );
    }

    public function test_create_fail(): void
    {
        $employees = $this->getEmployees();
        $response = $this->postJson('/api/v1/employees', [
            'first_name' => '',
            'last_name' => '',
            'company_id' => $employees[0]->company_id,
            'email' => $employees[0]->email,
            'phone' => $employees[0]->phone,
        ]);
        $response->assertStatus(422)
            ->assertJsonPath('errors.first_name', ['The first name field is required.'])
            ->assertJsonPath('errors.last_name', ['The last name field is required.']);
    }

    public function test_create_duplicate_employee_fail(): void
    {
        $employees = $this->getEmployees();
        $response = $this->postJson('/api/v1/employees', [
            'first_name' => $employees[0]->first_name,
            'last_name' => $employees[0]->last_name,
            'company_id' => $employees[0]->company_id,
            'email' => $employees[0]->email,
            'phone' => $employees[0]->phone,
        ]);
        $response->assertStatus(422)
            ->assertJsonPath('errors.first_name', ['The first name has already been taken.'])
            ->assertJsonPath('errors.last_name', ['The last name has already been taken.']);
    }

    public function test_update_success(): void
    {
        $employees = $this->getEmployees();
        $response = $this->patchJson('/api/v1/employees/' . $employees[0]->id, [
            'first_name' => $employees[0]->first_name,
            'last_name' => $employees[0]->last_name,
            'company_id' => $employees[0]->company_id,
            'email' => $employees[0]->email,
            'phone' => $employees[0]->phone,
        ]);
        $response->assertStatus(200)->assertJson(fn (AssertableJson $json) =>
                                            $json->where('first_name', $employees[0]->first_name)
                                            ->where('last_name', $employees[0]->last_name)
                                            ->where('company_id', $employees[0]->company_id)
                                            ->where('email', fn (string $email) => str($email)->is($employees[0]->email))
                                            ->where('phone', fn (string $phone) => str($phone)->is($employees[0]->phone))
                                            ->missingAll(['created_at', 'updated_at'])
                                            ->etc()
                                        );
    }

    public function test_update_fail(): void
    {
        $employees = $this->getEmployees();
        if (count($employees) < 2) {
            $this->test_create_success();
            $employees = $this->employee::get();
        }
        $response = $this->patchJson('/api/v1/employees/' . $employees[1]->id, [
            'first_name' => '',
            'last_name' => '',
            'company_id' => $employees[0]->company_id,
            'email' => $employees[0]->email,
            'phone' => $employees[0]->phone,
        ]);
        $response->assertStatus(422)
            ->assertJsonPath('errors.first_name', ['The first name field is required.'])
            ->assertJsonPath('errors.last_name', ['The last name field is required.']);
    }

    public function test_update_duplicate_emplyee_fail(): void
    {
        $employees = $this->getEmployees();
        if (count($employees) < 2) {
            $this->test_create_success();
            $employees = $this->employee::get();
        }
        $response = $this->patchJson('/api/v1/employees/' . $employees[1]->id, [
            'first_name' => $employees[0]->first_name,
            'last_name' => $employees[0]->last_name,
            'company_id' => $employees[1]->company_id,
            'email' => $employees[1]->email,
            'phone' => $employees[1]->phone,
        ]);
        $response->assertStatus(422)
            ->assertJsonPath('errors.first_name', ['The first name has already been taken.'])
            ->assertJsonPath('errors.last_name', ['The last name has already been taken.']);
    }

    public function test_get_success(): void
    {
        $employees = $this->getEmployees();
        $response = $this->getJson('/api/v1/employees/' . $employees[0]->id);
        $response->assertStatus(200)
                        ->assertJson(fn (AssertableJson $json) =>
                        $json->where('id', $employees[0]->id)->where('first_name', $employees[0]->first_name)
                        ->where('last_name', $employees[0]->last_name)
                        ->where('company_id', $employees[0]->company_id)
                        ->where('email', fn (string $email) => str($email)->is($employees[0]->email))
                        ->where('phone', fn (string $phone) => str($phone)->is($employees[0]->phone))
                        ->missingAll(['created_at', 'updated_at'])
                        ->etc()
                    );
    }

    public function test_get_fail(): void
    {
        $response = $this->getJson('/api/v1/employees/-1');
        $response->assertStatus(404);
    }

    public function test_delete_success(): void
    {
        list($found, $company_id, $first_name, $last_name, $email, $phone) = $this->getNonExistEmployee();
        if (!$found) {
            $this->assertTrue(FALSE);
            return;
        }
        $employee = $this->employee::create(array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company_id' => $company_id,
            'email' => $email,
            'phone' => $phone
        ));
        $response = $this->deleteJson('/api/v1/employees/' . $employee->id);
        $response->assertStatus(204);
    }

    public function test_delete_fail(): void
    {
        $response = $this->deleteJson('/api/v1/employees/-1');
        $response->assertStatus(204);
    }
}
