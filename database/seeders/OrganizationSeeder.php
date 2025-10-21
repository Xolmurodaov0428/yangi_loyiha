<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'Toshkent Axborot Texnologiyalari Universiteti',
                'address' => 'Toshkent sh., Amir Temur ko\'ch., 108',
                'phone' => '+998712387878',
                'email' => 'info@tuit.uz',
                'is_active' => true,
            ],
            [
                'name' => 'Uzinfocom',
                'address' => 'Toshkent sh., Shota Rustaveli ko\'ch., 5',
                'phone' => '+998712334455',
                'email' => 'info@uzinfocom.uz',
                'is_active' => true,
            ],
            [
                'name' => 'IT Park Uzbekistan',
                'address' => 'Toshkent sh., Amir Temur ko\'ch., 107B',
                'phone' => '+998712345678',
                'email' => 'info@itpark.uz',
                'is_active' => true,
            ],
            [
                'name' => 'UZCARD',
                'address' => 'Toshkent sh., Mirzo Ulug\'bek ko\'ch., 56',
                'phone' => '+998712876543',
                'email' => 'info@uzcard.uz',
                'is_active' => true,
            ],
            [
                'name' => 'Unicon.uz',
                'address' => 'Toshkent sh., Chilonzor tumani',
                'phone' => '+998712223344',
                'email' => 'info@unicon.uz',
                'is_active' => true,
            ],
        ];

        foreach ($organizations as $org) {
            Organization::create($org);
        }
    }
}
