<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'first_name' => 'Manila Arena',
            'last_name' => 'Food Court',
            'email' => 'manila_arena@email.com',
            'phone' => '09999999999',
            'address' => 'Manila Arena',
            'avatar' => '',
        ]);
    }
}
