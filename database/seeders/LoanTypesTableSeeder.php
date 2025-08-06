<?php

namespace Database\Seeders;

use App\Models\LoanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LoanTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         LoanType::updateOrCreate([
                'name' => 'Semana10',
                'calendar_days' => 7,
                'payments_total' => 10,
                'porcentage' => 25.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            LoanType::updateOrCreate([
                'name' => 'Semana15',
                'calendar_days' => 7,
                'payments_total' => 15,
                'porcentage' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            LoanType::updateOrCreate([
                'name' => 'Mensual10',
                'calendar_days' => 30,
                'payments_total' => 10,
                'porcentage' => 15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);       
    }
}
