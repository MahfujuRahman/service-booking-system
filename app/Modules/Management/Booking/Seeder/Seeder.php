<?php

namespace App\Modules\Management\Booking\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\Booking\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\Booking\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        for ($serviceId = 1; $serviceId <= 20; $serviceId++) {
            $numBookings = rand(2, 5);
            for ($i = 0; $i < $numBookings; $i++) {
                self::$model::create([
                    'user_id' => 2,
                    'service_id' => $serviceId,
                    'booking_date' => $faker->dateTimeBetween('tomorrow', '+1 year')->format('Y-m-d'),
                ]);
            }
        }
    }
}
