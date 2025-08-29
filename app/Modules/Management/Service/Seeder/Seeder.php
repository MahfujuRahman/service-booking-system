<?php

namespace App\Modules\Management\Service\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\Service\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\Service\Models\Model::class;

    public function run(): void
    {
        self::$model::truncate();

        $services = [
            [
                'name' => 'Web Development',
                'description' => 'Custom website design and development services.',
                'price' => 5000.00,
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Building native and cross-platform mobile applications.',
                'price' => 8000.00,
            ],
            [
                'name' => 'Cloud Computing Services',
                'description' => 'Migration and management of cloud infrastructure.',
                'price' => 6000.00,
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Protecting systems and data from cyber threats.',
                'price' => 7000.00,
            ],
            [
                'name' => 'Data Analytics',
                'description' => 'Analyzing data to provide actionable insights.',
                'price' => 4500.00,
            ],
            [
                'name' => 'IT Consulting',
                'description' => 'Expert advice on IT strategy and implementation.',
                'price' => 3000.00,
            ],
            [
                'name' => 'Software Maintenance',
                'description' => 'Ongoing support and updates for software systems.',
                'price' => 2500.00,
            ],
            [
                'name' => 'Network Infrastructure',
                'description' => 'Design and setup of robust network systems.',
                'price' => 5500.00,
            ],
            [
                'name' => 'AI and Machine Learning',
                'description' => 'Developing AI models and machine learning solutions.',
                'price' => 9000.00,
            ],
            [
                'name' => 'E-commerce Solutions',
                'description' => 'Building online stores and payment integrations.',
                'price' => 6500.00,
            ],
            [
                'name' => 'DevOps Services',
                'description' => 'Implementing CI/CD pipelines and infrastructure automation.',
                'price' => 7500.00,
            ],
            [
                'name' => 'Database Management',
                'description' => 'Optimizing and maintaining database systems for performance.',
                'price' => 4000.00,
            ],
            [
                'name' => 'UI/UX Design',
                'description' => 'Designing intuitive user interfaces and experiences.',
                'price' => 3500.00,
            ],
            [
                'name' => 'Blockchain Development',
                'description' => 'Building secure decentralized applications and smart contracts.',
                'price' => 10000.00,
            ],
            [
                'name' => 'IoT Solutions',
                'description' => 'Developing connected devices and IoT ecosystems.',
                'price' => 8500.00,
            ],
            [
                'name' => 'API Development',
                'description' => 'Creating and integrating RESTful and GraphQL APIs.',
                'price' => 5500.00,
            ],
            [
                'name' => 'Digital Marketing',
                'description' => 'Strategies for online presence and customer engagement.',
                'price' => 4000.00,
            ],
            [
                'name' => 'VR/AR Development',
                'description' => 'Creating immersive virtual and augmented reality experiences.',
                'price' => 9500.00,
            ],
            [
                'name' => 'Big Data Solutions',
                'description' => 'Processing and analyzing large-scale data sets.',
                'price' => 8000.00,
            ],
            [
                'name' => 'Quality Assurance',
                'description' => 'Testing and ensuring software reliability and performance.',
                'price' => 3000.00,
            ],
        ];

        foreach ($services as $service) {
            self::$model::create($service);
        }
    }
}
