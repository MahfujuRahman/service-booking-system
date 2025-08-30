<?php

namespace Database\Seeders;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;

/**
 * User seeder management.
 */

use App\Modules\Management\UserManagement\Role\Seeder\Seeder as RoleSeeder;
use App\Modules\Management\UserManagement\User\Seeder\Seeder as UserSeeder;
use App\Modules\Management\SettingManagement\WebsiteSettings\Seeder\Seeder as WebsiteSettingsSeeder;
use App\Modules\Management\Booking\Seeder\Seeder as BookingSeeder;
use App\Modules\Management\Service\Seeder\Seeder as ServiceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
      
            RoleSeeder::class,
            UserSeeder::class,
            WebsiteSettingsSeeder::class,
            BookingSeeder::class,
            ServiceSeeder::class,

        ]);
    }
}
