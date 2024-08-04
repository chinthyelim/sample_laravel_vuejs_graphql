<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected function createFakeCompany(): Collection
    {
        $companies = Company::get();
        if (!count($companies)) {
            Company::factory()->create();
            $companies = Company::get();
        }
        return $companies;
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companies = $this->createFakeCompany();
        return [
            'first_name' => fake()->unique()->name(),
            'last_name' => fake()->unique()->name(),
            'company_id' => $companies[0]->id,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
        ];
    }
}
