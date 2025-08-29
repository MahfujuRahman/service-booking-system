<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\Management\UserManagement\User\Models\Model as User;
use App\Modules\Management\Booking\Models\Model as Booking;
use App\Modules\Management\Service\Models\Model as Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingModelTest extends TestCase
{
    protected $user;
    protected $admin;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        Booking::truncate();
        Service::truncate();

        $this->user = User::create([
            'first_name' => 'Customer',
            'last_name' => 'Test',
            'email' => 'customer@example.com',
            'role_id' => 2,
            'status' => 'active'
        ]);

        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'email' => 'admin@example.com',
            'role_id' => 1,
            'status' => 'active'
        ]);

        $this->service = Service::create([
            'name' => 'Test Service',
            'price' => 150.00,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function booking_can_be_created()
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
        ]);
    }

    /** @test */
    public function booking_belongs_to_user()
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        $this->assertInstanceOf(User::class, $booking->user);
        $this->assertEquals($this->user->id, $booking->user->id);
    }

    /** @test */
    public function booking_belongs_to_service()
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        $this->assertInstanceOf(Service::class, $booking->service);
        $this->assertEquals($this->service->id, $booking->service->id);
    }

    /** @test */
    public function booking_has_required_attributes()
    {
        $booking = new Booking();

        $this->assertEquals('bookings', $booking->getTable());
        $this->assertTrue($booking->usesTimestamps());
    }

    /** @test */
    public function booking_uses_soft_deletes()
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        $booking->delete();

        $this->assertSoftDeleted('bookings', [
            'id' => $booking->id
        ]);

        // Booking should not appear in normal queries
        $this->assertCount(0, Booking::all());

        // But should appear when including trashed
        $this->assertCount(1, Booking::withTrashed()->get());
    }

    /** @test */
    public function booking_can_be_scoped_by_active_status()
    {
        Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
            'status' => 'active'
        ]);

        Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
            'status' => 'inactive'
        ]);

        $activeBookings = Booking::active()->get();

        $this->assertCount(1, $activeBookings);
        $this->assertEquals('active', $activeBookings->first()->status);
    }

    /** @test */
    public function booking_can_be_scoped_by_inactive_status()
    {
        Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
            'status' => 'active'
        ]);

        Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
            'status' => 'inactive'
        ]);

        $inactiveBookings = Booking::inactive()->get();

        $this->assertCount(1, $inactiveBookings);
        $this->assertEquals('inactive', $inactiveBookings->first()->status);
    }

    /** @test */
    public function booking_defaults_to_null_status()
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        // Since we're not setting defaults, status should be null initially
        $this->assertNull($booking->status);
    }

    /** @test */
    public function booking_stores_booking_date()
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::parse('2024-12-25'),
        ]);

        $this->assertEquals('2024-12-25', Carbon::parse($booking->fresh()->booking_date)->format('Y-m-d'));
    }

    /** @test */
    public function booking_handles_null_values_gracefully()
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        $this->assertNull($booking->notes);
        $this->assertNull($booking->admin_notes);
    }

    /** @test */
    public function booking_can_check_if_past_due()
    {
        // Create booking in the past
        $pastBooking = Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::yesterday(),
        ]);

        // Create booking in the future
        $futureBooking = Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        // Assuming there's a method to check if booking is past due
        // This would depend on your actual implementation
        $this->assertTrue($pastBooking->booking_date->isPast());
        $this->assertFalse($futureBooking->booking_date->isPast());
    }

    /** @test */
    public function user_can_have_multiple_bookings()
    {
        $secondService = Service::create([
            'name' => 'Second Service',
            'price' => 200.00,
            'status' => 'active'
        ]);

        Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $secondService->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        $userBookings = Booking::where('user_id', $this->user->id)->get();
        $this->assertCount(2, $userBookings);
    }

    /** @test */
    public function service_can_have_multiple_bookings()
    {
        $secondUser = User::create([
            'first_name' => 'Customer2',
            'last_name' => 'Test',
            'email' => 'customer2@example.com',
            'role_id' => 2,
            'status' => 'active'
        ]);

        Booking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        Booking::create([
            'user_id' => $secondUser->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::tomorrow(),
        ]);

        $serviceBookings = Booking::where('service_id', $this->service->id)->get();
        $this->assertCount(2, $serviceBookings);
    }
}
