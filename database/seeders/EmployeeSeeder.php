<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devisi = Divisi::all();

        if ($devisi->isEmpty()) {
            $this->call(DivisiSeeder::class);
            $devisi = Divisi::all();
        }

        $employees = [
            [
                'name' => 'John Doe',
                'image' => 'https://example.com/photos/john.jpg',
                'phone' => '123-456-7890',
                'position' => 'Developer',
                'divisi_id' => $devisi->random()->id,
            ],
            [
                'name' => 'Jane Smith',
                'image' => 'https://example.com/photos/jane.jpg',
                'phone' => '987-654-3210',
                'position' => 'Designer',
                'divisi_id' => $devisi->random()->id,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create([
                'id' => Str::uuid(),
                'name' => $employee['name'],
                'image' => $employee['image'],
                'phone' => $employee['phone'],
                'position' => $employee['position'],
                'divisi_id' => $employee['divisi_id'],
            ]);
        }
    }
}
