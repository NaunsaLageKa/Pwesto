<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FloorPlan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a hub owner user if it doesn't exist
        $hubOwner = User::firstOrCreate(
            ['email' => 'hubowner@example.com'],
            [
                'name' => 'Hub Owner',
                'email' => 'hubowner@example.com',
                'password' => bcrypt('password'),
                'role' => 'hub_owner',
                'status' => 'approved',
            ]
        );

        // Create a default floor plan if it doesn't exist
        FloorPlan::firstOrCreate(
            ['hub_owner_id' => $hubOwner->id, 'is_active' => true],
            [
                'name' => 'Default Floor Plan',
                'layout_data' => [
                    [
                        'shape' => 'desk',
                        'x' => 50,
                        'y' => 50,
                        'id' => 1,
                        'rotation' => 0,
                        'width' => 80,
                        'height' => 60,
                        'label' => 'Desk 1'
                    ],
                    [
                        'shape' => 'desk',
                        'x' => 150,
                        'y' => 50,
                        'id' => 2,
                        'rotation' => 0,
                        'width' => 80,
                        'height' => 60,
                        'label' => 'Desk 2'
                    ],
                    [
                        'shape' => 'desk',
                        'x' => 250,
                        'y' => 50,
                        'id' => 3,
                        'rotation' => 0,
                        'width' => 80,
                        'height' => 60,
                        'label' => 'Desk 3'
                    ],
                    [
                        'shape' => 'desk',
                        'x' => 350,
                        'y' => 50,
                        'id' => 4,
                        'rotation' => 0,
                        'width' => 80,
                        'height' => 60,
                        'label' => 'Desk 4'
                    ],
                    [
                        'shape' => 'desk',
                        'x' => 50,
                        'y' => 150,
                        'id' => 5,
                        'rotation' => 0,
                        'width' => 80,
                        'height' => 60,
                        'label' => 'Desk 5'
                    ],
                    [
                        'shape' => 'desk',
                        'x' => 150,
                        'y' => 150,
                        'id' => 6,
                        'rotation' => 0,
                        'width' => 80,
                        'height' => 60,
                        'label' => 'Desk 6'
                    ],
                    [
                        'shape' => 'desk',
                        'x' => 250,
                        'y' => 150,
                        'id' => 7,
                        'rotation' => 0,
                        'width' => 80,
                        'height' => 60,
                        'label' => 'Desk 7'
                    ],
                    [
                        'shape' => 'desk',
                        'x' => 350,
                        'y' => 150,
                        'id' => 8,
                        'rotation' => 0,
                        'width' => 80,
                        'height' => 60,
                        'label' => 'Desk 8'
                    ],
                    [
                        'shape' => 'chair',
                        'x' => 70,
                        'y' => 70,
                        'id' => 9,
                        'rotation' => 0,
                        'width' => 40,
                        'height' => 40,
                        'label' => 'Chair 1'
                    ],
                    [
                        'shape' => 'chair',
                        'x' => 170,
                        'y' => 70,
                        'id' => 10,
                        'rotation' => 0,
                        'width' => 40,
                        'height' => 40,
                        'label' => 'Chair 2'
                    ],
                    [
                        'shape' => 'chair',
                        'x' => 270,
                        'y' => 70,
                        'id' => 11,
                        'rotation' => 0,
                        'width' => 40,
                        'height' => 40,
                        'label' => 'Chair 3'
                    ],
                    [
                        'shape' => 'chair',
                        'x' => 370,
                        'y' => 70,
                        'id' => 12,
                        'rotation' => 0,
                        'width' => 40,
                        'height' => 40,
                        'label' => 'Chair 4'
                    ],
                    [
                        'shape' => 'chair',
                        'x' => 70,
                        'y' => 170,
                        'id' => 13,
                        'rotation' => 0,
                        'width' => 40,
                        'height' => 40,
                        'label' => 'Chair 5'
                    ],
                    [
                        'shape' => 'chair',
                        'x' => 170,
                        'y' => 170,
                        'id' => 14,
                        'rotation' => 0,
                        'width' => 40,
                        'height' => 40,
                        'label' => 'Chair 5'
                    ],
                    [
                        'shape' => 'chair',
                        'x' => 270,
                        'y' => 170,
                        'id' => 15,
                        'rotation' => 0,
                        'width' => 40,
                        'height' => 40,
                        'label' => 'Chair 7'
                    ],
                    [
                        'shape' => 'chair',
                        'x' => 370,
                        'y' => 170,
                        'id' => 16,
                        'rotation' => 0,
                        'width' => 40,
                        'height' => 40,
                        'label' => 'Chair 8'
                    ],
                ],
                'description' => 'Default floor plan with desks and chairs for booking',
            ]
        );
    }
}
