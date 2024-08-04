<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrayLogo = array(
            'bendigobank-logo.jpg',
            'BMW_White_Logo.jpg',
            'brand_mark_vertical_primary_mdp-01.jpg',
            'commBank-logo.jpg',
            'honda-auto-logo--large.jpg',
            'logo-promo-anz-small.jpg',
            'Macquarie-logo.jpg',
            'MyState_Logo_s.jpg',
            'nab-logo.jpg',
            'Toyota_Logo_Flat_56x47.jpg'
            );
        for ($i=0; $i < 10; $i++) {
            $companyName = fake()->regexify('[A-Za-z0-9]{15}');
            Company::create([
                'name' => fake()->unique()->name(),
                'email' => fake()->unique()->safeEmail(),
                'logo' => $arrayLogo[$i],
                'website' => "https://$companyName.com.au"
            ]);
        }

    }
}
