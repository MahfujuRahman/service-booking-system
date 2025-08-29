<?php

namespace App\Modules\Management\Dashboard\Actions;

use App\Modules\Management\UserManagement\User\Models\Model as User;
use App\Modules\Management\Service\Models\Model as Service;
use App\Modules\Management\Booking\Models\Model as Booking;

class GetAllDashboardData
{


    public static function execute()
    {
        try {

            $income = Booking::join('services', 'bookings.service_id', '=', 'services.id')->sum('services.price');
            $latest_booking = Booking::with(['user:id,first_name,last_name', 'service:id,name'])->latest()->take(10)->get();
            $data = [
                'total_users' => User::count(),
                'total_services' => Service::count(),
                'total_bookings' => Booking::count(),
                'total_income' => $income,
                'latest_bookings' => $latest_booking,
            ];

            return entityResponse($data);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
