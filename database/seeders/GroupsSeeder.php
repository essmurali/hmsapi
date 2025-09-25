<?php

namespace Database\Seeders;

use App\Models\Groups;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Top-level categories
        $hospitalA = Groups::create(['name' => 'Hospital A', ]);
        $hospitalB = Groups::create(['name' => 'Hospital B']);

        // Second-level categories
        $department = $hospitalA->children()->create(['name' => 'Stomach'], ['name' => 'Shoulder'], ['name' => 'Knee']);
        $hospitalB->children()->create(['name' => 'Gaming addiction'], ['name' => 'Anxiety'], ['name' => 'Depression']);

        // Third-level categories
        $department->children()->createMany([
            ['name' => 'Crohn\'s Disease'],
            ['name' => 'Ulcerative Colitis'],
        ]);
    }
}
