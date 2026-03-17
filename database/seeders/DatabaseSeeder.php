<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Greenhouse;
use App\Models\Bay;
use App\Models\JobType;
use App\Models\DropdownList;
use App\Models\DropdownValue;
use App\Models\JobField;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@greenhouse.com',
            'password' => bcrypt('@Dmin@Dev007'),
            'role' => 'admin'
        ]);

        // Create worker user
        $worker = User::create([
            'name' => 'Worker User',
            'email' => 'worker@greenhouse.com',
            'password' => bcrypt('password'),
            'role' => 'worker'
        ]);

        // Create greenhouse
        $greenhouse = Greenhouse::create([
            'name' => '72nd Street Greenhouse',
            'description' => 'Main greenhouse facility',
            'address' => '123 Greenhouse Lane',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip' => '62701'
        ]);

        // Create bays
        $bays = [
            'Shipping Area',
            'Range',
            'Hot House',
            'Hoop House 1',
            'Hoop House 2',
            'Hoop House 3',
            'Hoop House 4',
            'Hoop House 5'
        ];

        foreach ($bays as $bayName) {
            Bay::create([
                'greenhouse_id' => $greenhouse->id,
                'name' => $bayName,
                'description' => $bayName . ' bay'
            ]);
        }

        // Create dropdown lists
        $plantsList = DropdownList::create(['name' => 'Plants', 'description' => 'List of plants']);
        $applicationMethodsList = DropdownList::create(['name' => 'Application Methods', 'description' => 'Chemical application methods']);
        $chemicalsList = DropdownList::create(['name' => 'Chemicals', 'description' => 'List of chemicals']);
        $moistureLevelsList = DropdownList::create(['name' => 'Moisture Levels', 'description' => 'Soil moisture levels']);
        $ventStatusList = DropdownList::create(['name' => 'Vent Status', 'description' => 'Vent open/closed status']);

        // Plants values
        $plants = ['Tomato', 'Pepper', 'Lettuce', 'Basil', 'Cucumber', 'Strawberry'];
        foreach ($plants as $index => $plant) {
            DropdownValue::create([
                'dropdown_list_id' => $plantsList->id,
                'value' => strtolower($plant),
                'label' => $plant,
                'sort_order' => $index
            ]);
        }

        // Application methods
        $methods = ['Sprayer', 'Drench', 'Foliar', 'Soil injection'];
        foreach ($methods as $index => $method) {
            DropdownValue::create([
                'dropdown_list_id' => $applicationMethodsList->id,
                'value' => strtolower($method),
                'label' => $method,
                'sort_order' => $index
            ]);
        }

        // Chemicals
        $chemicals = ['Fertilizer A', 'Fertilizer B', 'Pesticide X', 'Fungicide Y'];
        foreach ($chemicals as $index => $chemical) {
            DropdownValue::create([
                'dropdown_list_id' => $chemicalsList->id,
                'value' => strtolower(str_replace(' ', '_', $chemical)),
                'label' => $chemical,
                'sort_order' => $index
            ]);
        }

        // Moisture levels
        $levels = ['Dry', 'Moist', 'Wet', 'Saturated'];
        foreach ($levels as $index => $level) {
            DropdownValue::create([
                'dropdown_list_id' => $moistureLevelsList->id,
                'value' => strtolower($level),
                'label' => $level,
                'sort_order' => $index
            ]);
        }

        // Vent status
        $ventStatus = ['Open', 'Closed', 'Partially Open'];
        foreach ($ventStatus as $index => $status) {
            DropdownValue::create([
                'dropdown_list_id' => $ventStatusList->id,
                'value' => strtolower($status),
                'label' => $status,
                'sort_order' => $index
            ]);
        }

        // Create job types
        $chemicalsJob = JobType::create([
            'name' => 'Chemicals',
            'description' => 'Chemical application tasks',
            'icon' => 'beaker',
            'is_active' => true,
            'sort_order' => 1
        ]);

        $cropWalkJob = JobType::create([
            'name' => 'Crop Walk',
            'description' => 'Crop inspection and monitoring',
            'icon' => 'clipboard',
            'is_active' => true,
            'sort_order' => 2
        ]);

        $soilSamplesJob = JobType::create([
            'name' => 'Soil Samples',
            'description' => 'Soil sampling and testing',
            'icon' => 'beaker',
            'is_active' => true,
            'sort_order' => 3
        ]);

        $growthTrackingJob = JobType::create([
            'name' => 'Growth Tracking',
            'description' => 'Plant growth monitoring',
            'icon' => 'chart-bar',
            'is_active' => true,
            'sort_order' => 4
        ]);

        // Create job fields for Chemicals
        $chemicalFields = [
            [
                'label' => 'Plant',
                'field_name' => 'plant',
                'field_type' => 'dropdown',
                'is_required' => true,
                'dropdown_source_id' => $plantsList->id,
                'sort_order' => 1
            ],
            [
                'label' => 'Application Method',
                'field_name' => 'application_method',
                'field_type' => 'dropdown',
                'is_required' => true,
                'dropdown_source_id' => $applicationMethodsList->id,
                'sort_order' => 2
            ],
            [
                'label' => 'OZ per Pot',
                'field_name' => 'oz_per_pot',
                'field_type' => 'number',
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'label' => 'Chemical Applied',
                'field_name' => 'chemical_applied',
                'field_type' => 'dropdown',
                'is_required' => true,
                'dropdown_source_id' => $chemicalsList->id,
                'sort_order' => 4
            ],
            [
                'label' => 'OZ per 100 Gallons',
                'field_name' => 'oz_per_100_gallons',
                'field_type' => 'number',
                'is_required' => true,
                'sort_order' => 5
            ],
            [
                'label' => 'Injector Rate',
                'field_name' => 'injector_rate',
                'field_type' => 'number',
                'is_required' => true,
                'sort_order' => 6
            ],
            [
                'label' => 'PPM',
                'field_name' => 'ppm',
                'field_type' => 'ppm',
                'is_required' => true,
                'sort_order' => 7
            ],
            [
                'label' => 'Notes',
                'field_name' => 'notes',
                'field_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 8
            ]
        ];

        foreach ($chemicalFields as $field) {
            $field['job_type_id'] = $chemicalsJob->id;
            JobField::create($field);
        }

        // Create job fields for Crop Walk
        $cropWalkFields = [
            [
                'label' => 'Current Temperature',
                'field_name' => 'current_temp',
                'field_type' => 'temperature',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'label' => 'High Temperature',
                'field_name' => 'high_temp',
                'field_type' => 'temperature',
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'label' => 'Low Temperature',
                'field_name' => 'low_temp',
                'field_type' => 'temperature',
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'label' => 'Is Heat On?',
                'field_name' => 'heat_on',
                'field_type' => 'yes_no',
                'is_required' => true,
                'sort_order' => 4
            ],
            [
                'label' => 'Should Heat Be On?',
                'field_name' => 'heat_should_be_on',
                'field_type' => 'yes_no',
                'is_required' => true,
                'sort_order' => 5
            ],
            [
                'label' => 'Vents Status',
                'field_name' => 'vents_status',
                'field_type' => 'dropdown',
                'is_required' => true,
                'dropdown_source_id' => $ventStatusList->id,
                'sort_order' => 6
            ],
            [
                'label' => 'Should Vents Be?',
                'field_name' => 'vents_should_be',
                'field_type' => 'dropdown',
                'is_required' => true,
                'dropdown_source_id' => $ventStatusList->id,
                'sort_order' => 7
            ],
            [
                'label' => 'Signs of Wilting',
                'field_name' => 'wilting',
                'field_type' => 'yes_no',
                'is_required' => true,
                'sort_order' => 8
            ],
            [
                'label' => 'Moisture Level',
                'field_name' => 'moisture_level',
                'field_type' => 'dropdown',
                'is_required' => true,
                'dropdown_source_id' => $moistureLevelsList->id,
                'sort_order' => 9
            ],
            [
                'label' => 'Disease or Pests',
                'field_name' => 'disease_pests',
                'field_type' => 'yes_no',
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'label' => 'Explanation',
                'field_name' => 'explanation',
                'field_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 11
            ]
        ];

        foreach ($cropWalkFields as $field) {
            $field['job_type_id'] = $cropWalkJob->id;
            JobField::create($field);
        }

        // Create job fields for Soil Samples
        $soilSampleFields = [
            [
                'label' => 'Week',
                'field_name' => 'week',
                'field_type' => 'number',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'label' => 'Plant',
                'field_name' => 'plant',
                'field_type' => 'dropdown',
                'is_required' => true,
                'dropdown_source_id' => $plantsList->id,
                'sort_order' => 2
            ],
            [
                'label' => 'pH Level',
                'field_name' => 'ph',
                'field_type' => 'ph',
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'label' => 'EC Level',
                'field_name' => 'ec',
                'field_type' => 'ec',
                'is_required' => true,
                'sort_order' => 4
            ],
            [
                'label' => 'Photo of Plant',
                'field_name' => 'plant_photo',
                'field_type' => 'photo',
                'is_required' => false,
                'sort_order' => 5
            ]
        ];

        foreach ($soilSampleFields as $field) {
            $field['job_type_id'] = $soilSamplesJob->id;
            JobField::create($field);
        }

        // Create job fields for Growth Tracking
        $growthTrackingFields = [
            [
                'label' => 'Week',
                'field_name' => 'week',
                'field_type' => 'number',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'label' => 'Plant',
                'field_name' => 'plant',
                'field_type' => 'dropdown',
                'is_required' => true,
                'dropdown_source_id' => $plantsList->id,
                'sort_order' => 2
            ],
            [
                'label' => 'Group Photo',
                'field_name' => 'group_photo',
                'field_type' => 'photo',
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'label' => 'Height Photo',
                'field_name' => 'height_photo',
                'field_type' => 'photo',
                'is_required' => false,
                'sort_order' => 4
            ],
            [
                'label' => 'Width Photo',
                'field_name' => 'width_photo',
                'field_type' => 'photo',
                'is_required' => false,
                'sort_order' => 5
            ]
        ];

        foreach ($growthTrackingFields as $field) {
            $field['job_type_id'] = $growthTrackingJob->id;
            JobField::create($field);
        }

        $this->call([
            // Your existing seeders
            UserAccessSeeder::class, // Add this at the end
        ]);
    }
}