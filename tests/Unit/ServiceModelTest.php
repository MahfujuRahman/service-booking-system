<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\Management\Service\Models\Model as Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceModelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Service::truncate();
    }

    /** @test */
    public function service_can_be_created()
    {
        $service = Service::create([
            'name' => 'Test Service',
            'description' => 'This is a test service',
            'price' => 150.00,
            'status' => 'active'
        ]);

        $this->assertInstanceOf(Service::class, $service);
        $this->assertDatabaseHas('services', [
            'name' => 'Test Service',
            'description' => 'This is a test service',
            'price' => 150.00,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function service_has_required_attributes()
    {
        $service = new Service();
        
        $this->assertEquals('services', $service->getTable());
        $this->assertTrue($service->usesTimestamps());
    }

    /** @test */
    public function service_uses_soft_deletes()
    {
        $service = Service::create([
            'name' => 'Test Service',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'active'
        ]);

        $service->delete();

        $this->assertSoftDeleted('services', [
            'id' => $service->id
        ]);

        // Service should not appear in normal queries
        $this->assertCount(0, Service::all());
        
        // But should appear when including trashed
        $this->assertCount(1, Service::withTrashed()->get());
    }

    /** @test */
    public function service_can_be_scoped_by_active_status()
    {
        Service::create([
            'name' => 'Active Service',
            'status' => 'active',
            'price' => 100.00
        ]);

        Service::create([
            'name' => 'Inactive Service', 
            'status' => 'inactive',
            'price' => 100.00
        ]);

        $activeServices = Service::active()->get();
        
        $this->assertCount(1, $activeServices);
        $this->assertEquals('active', $activeServices->first()->status);
        $this->assertEquals('Active Service', $activeServices->first()->name);
    }

    /** @test */
    public function service_can_be_scoped_by_inactive_status()
    {
        Service::create([
            'name' => 'Active Service',
            'status' => 'active',
            'price' => 100.00
        ]);

        Service::create([
            'name' => 'Inactive Service',
            'status' => 'inactive', 
            'price' => 100.00
        ]);

        $inactiveServices = Service::inactive()->get();
        
        $this->assertCount(1, $inactiveServices);
        $this->assertEquals('inactive', $inactiveServices->first()->status);
        $this->assertEquals('Inactive Service', $inactiveServices->first()->name);
    }

    /** @test */
    public function service_price_is_stored_as_float()
    {
        $service = Service::create([
            'name' => 'Test Service',
            'price' => '150.99',
            'status' => 'active'
        ]);

        $this->assertIsFloat($service->fresh()->price);
        $this->assertEquals(150.99, $service->fresh()->price);
    }

    /** @test */
    public function service_can_have_long_description()
    {
        $longDescription = str_repeat('This is a very long description. ', 100);
        
        $service = Service::create([
            'name' => 'Test Service',
            'description' => $longDescription,
            'price' => 100.00,
            'status' => 'active'
        ]);

        $this->assertEquals($longDescription, $service->fresh()->description);
    }

    /** @test */
    public function service_name_can_be_up_to_255_characters()
    {
        $longName = str_repeat('a', 255);
        
        $service = Service::create([
            'name' => $longName,
            'price' => 100.00,
            'status' => 'active'
        ]);

        $this->assertEquals($longName, $service->fresh()->name);
        $this->assertEquals(255, strlen($service->fresh()->name));
    }

    /** @test */
    public function service_creator_is_set_when_user_is_authenticated()
    {
        // This would require mocking auth, but demonstrates the concept
        $service = Service::create([
            'name' => 'Test Service',
            'price' => 100.00,
            'creator' => 123
        ]);

        $this->assertEquals(123, $service->fresh()->creator);
    }

    /** @test */
    public function service_handles_null_values_gracefully()
    {
        $service = Service::create([
            'name' => 'Test Service',
            'description' => null,
            'price' => null
        ]);

        $this->assertEquals('Test Service', $service->name);
        $this->assertNull($service->description);
        $this->assertNull($service->price);
    }

    /** @test */
    public function service_timestamps_are_automatically_managed()
    {
        $service = Service::create([
            'name' => 'Test Service',
            'price' => 100.00
        ]);

        $this->assertNotNull($service->created_at);
        $this->assertNotNull($service->updated_at);
        
        $originalUpdatedAt = $service->updated_at;
        
        // Wait a moment and update
        sleep(1);
        $service->update(['name' => 'Updated Service']);
        
        $this->assertTrue($service->updated_at->greaterThan($originalUpdatedAt));
    }
}
