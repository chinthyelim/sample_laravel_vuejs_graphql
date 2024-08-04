<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;
//use Illuminate\Support\Facades\Storage;

class CompanyTest extends ApiTestCase
{
    public function test_get_full_list_success(): void
    {
        $companies = $this->company::all();
        $response = $this->getJson('/api/v1/companies');
        $response->assertStatus(200);
        $this->assertEquals($companies->count(), count($response->json()['data']), "actual value is equal to expected");
    }

    public function test_get_custom_list_success(): void
    {
        $paramString = http_build_query([ 'current_page_number' => 1, 'rows_per_page' => 2]);
        $response = $this->getJson("/api/v1/companies?$paramString");
        $response->assertStatus(200);
        $this->assertLessThanOrEqual(2, count($response->json()['data']), "actual value is Less than or equal to expected");
    }

    public function test_create_success(): void
    {
        list($found, $name, $email, $logo, $website) = $this->getNonExistCompany();
        if (!$found) {
            $this->assertTrue(FALSE);
            return;
        }
        $response = $this->postJson('/api/v1/companies', [
            'name' => $name,
            'email' => $email,
            'logo' => $logo,
            'website' => $website,
            'logoContent' => $this->dummyBase64LogoImage()
        ]);
        $response->assertStatus(201)
                    ->assertJson(fn (AssertableJson $json) =>
                        $json->where('name', $name)
                            ->where('email', fn (string $email) => str($email)->is($email))
                            ->where('logo', $logo)
                            ->where('website', $website)
                            ->missingAll(['created_at', 'updated_at'])
                            ->etc()
                    );
        // Laravel bug, https://laravel.com/docs/10.x/http-tests#testing-file-uploads
        //Storage::disk('public')->assertExists($logo);
    }

    public function test_create_fail(): void
    {
        $companies = $this->getCompanies();
        $response = $this->postJson('/api/v1/companies', [
            'name' => $companies[0]->name,
            'email' => $companies[0]->email,
            'logo' => $companies[0]->logo,
            'website' => $companies[0]->website . " 123",
            'logoContent' => $this->dummyBase64LogoImage()
        ]);
        $response->assertStatus(422)
            ->assertJsonPath('errors.name', ['The name has already been taken.'])
            ->assertJsonPath('errors.email', ['The email has already been taken.'])
            ->assertJsonPath('errors.website', ['The website field format is invalid.']);
    }

    public function test_update_success(): void
    {
        $companies = $this->getCompanies();
        $response = $this->patchJson('/api/v1/companies/' . $companies[0]->id, [
            'name' => $companies[0]->name,
            'email' => $companies[0]->email,
            'logo' => $companies[0]->logo,
            'website' => $companies[0]->website,
        ]);
        $newImageFileName = explode('.', $companies[0]->logo);
        array_pop($newImageFileName);
        $response->assertStatus(200)->assertJson(fn (AssertableJson $json) =>
                                            $json->where('name', $companies[0]->name)
                                                ->where('email', fn (string $email) => str($email)->is($companies[0]->email))
                                                ->where('logo', implode('.', $newImageFileName) . '.png')
                                                ->where('website', $companies[0]->website)
                                                ->missingAll(['created_at', 'updated_at'])
                                                ->etc()
                                        );
        // Laravel bug, https://laravel.com/docs/10.x/http-tests#testing-file-uploads
        //Storage::disk('public')->assertExists($logo);
    }

    public function test_update_fail(): void
    {
        $companies = $this->getCompanies();
        if (count($companies) < 2) {
            $this->test_create_success();
            $companies = $this->company::get();
        }
        $response = $this->patchJson('/api/v1/companies/' . $companies[1]->id, [
            'name' => $companies[0]->name,
            'email' => $companies[0]->email,
            'logo' => $companies[0]->logo,
            'website' => $companies[0]->website . " 456",
        ]);
        $response->assertStatus(422)
            ->assertJsonPath('errors.name', ['The name has already been taken.'])
            ->assertJsonPath('errors.email', ['The email has already been taken.'])
            ->assertJsonPath('errors.website', ['The website field format is invalid.']);
    }

    public function test_get_success(): void
    {
        $companies = $this->getCompanies();
        $response = $this->getJson('/api/v1/companies/' . $companies[0]->id);
        $response->assertStatus(200)
                    ->assertJson(fn (AssertableJson $json) =>
                        $json->where('id', $companies[0]->id)
                            ->where('name', $companies[0]->name)
                            ->where('email', fn (string $email) => str($email)->is($companies[0]->email))
                            ->missingAll(['created_at', 'updated_at'])
                            ->etc()
        );
    }

    public function test_get_fail(): void
    {
        $response = $this->getJson('/api/v1/companies/-1');
        $response->assertStatus(404);
    }

    public function test_delete_success(): void
    {
        list($found, $name, $email, $logo, $website) = $this->getNonExistCompany();
        if (!$found) {
            $this->assertTrue(FALSE);
            return;
        }
        $company = $this->company::create(array(
            'name' => $name,
            'email' => $email,
            'logo' => $logo,
            'website' => $website
        ));
        $response = $this->deleteJson('/api/v1/companies/' . $company->id);
        $response->assertStatus(204);
    }

    public function test_delete_fail(): void
    {
        list($found, $company_id, $first_name, $last_name, $email, $phone) = $this->getNonExistEmployee();
        if (!$found) {
            $this->assertTrue(FALSE);
            return;
        }
        $this->employee::create(array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company_id' => $company_id,
            'email' => $email,
            'phone' => $phone
        ));
        $response = $this->deleteJson('/api/v1/companies/' . $company_id);
        $response->assertStatus(400);
    }
}
