<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\WorkshopService;
use App\Models\Workshop;
use App\Models\User;
use App\Models\Participant;
use App\Models\TicketType;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Exception;

class WorkshopServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WorkshopService $workshopService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workshopService = new WorkshopService();
        $this->user = User::factory()->create();
    }

    public function test_create_workshop_successfully()
    {
        $data = [
            'name' => 'Test Workshop',
            'description' => 'Test Description',
            'start_date' => '2024-12-01 09:00:00',
            'end_date' => '2024-12-01 17:00:00',
            'location' => 'Test Location',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ];

        $workshop = $this->workshopService->createWorkshop($data);

        $this->assertInstanceOf(Workshop::class, $workshop);
        $this->assertEquals($data['name'], $workshop->name);
        $this->assertEquals($data['description'], $workshop->description);
        $this->assertEquals($data['location'], $workshop->location);
        $this->assertEquals($data['status'], $workshop->status);
        $this->assertEquals($data['created_by'], $workshop->created_by);
        $this->assertDatabaseHas('workshops', ['name' => $data['name']]);
    }

    public function test_create_workshop_with_organizers()
    {
        $organizer1 = User::factory()->create();
        $organizer2 = User::factory()->create();

        $data = [
            'name' => 'Test Workshop with Organizers',
            'description' => 'Test Description',
            'start_date' => '2024-12-01 09:00:00',
            'end_date' => '2024-12-01 17:00:00',
            'location' => 'Test Location',
            'created_by' => $this->user->id,
            'organizers' => [$organizer1->id, $organizer2->id],
        ];

        $workshop = $this->workshopService->createWorkshop($data);

        $this->assertCount(2, $workshop->organizers);
        $this->assertTrue($workshop->organizers->contains($organizer1));
        $this->assertTrue($workshop->organizers->contains($organizer2));
    }

    public function test_update_workshop_successfully()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $updateData = [
            'name' => 'Updated Workshop Name',
            'description' => 'Updated Description',
            'location' => 'Updated Location',
        ];

        $updatedWorkshop = $this->workshopService->updateWorkshop($workshop, $updateData);

        $this->assertEquals($updateData['name'], $updatedWorkshop->name);
        $this->assertEquals($updateData['description'], $updatedWorkshop->description);
        $this->assertEquals($updateData['location'], $updatedWorkshop->location);
        $this->assertDatabaseHas('workshops', $updateData);
    }

    public function test_delete_workshop_without_participants()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);

        $result = $this->workshopService->deleteWorkshop($workshop);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('workshops', ['id' => $workshop->id]);
    }

    public function test_delete_workshop_with_participants_fails()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete workshop with existing participants');

        $this->workshopService->deleteWorkshop($workshop);
    }

    public function test_get_workshop_statistics()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $ticketType = TicketType::factory()->create([
            'workshop_id' => $workshop->id,
            'price' => 100.00
        ]);
        
        // Create participants with different statuses
        Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => true,
            'is_checked_in' => true,
        ]);
        Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => false,
            'is_checked_in' => false,
        ]);

        $stats = $this->workshopService->getWorkshopStatistics($workshop);

        $this->assertIsArray($stats);
        $this->assertEquals($workshop->id, $stats['workshop_id']);
        $this->assertEquals($workshop->name, $stats['workshop_name']);
        $this->assertEquals(2, $stats['total_participants']);
        $this->assertEquals(1, $stats['paid_participants']);
        $this->assertEquals(1, $stats['checked_in_participants']);
        $this->assertEquals(100.00, $stats['total_revenue']);
        $this->assertEquals(50.0, $stats['check_in_rate']);
        $this->assertEquals(50.0, $stats['payment_rate']);
        $this->assertArrayHasKey('ticket_types', $stats);
        $this->assertArrayHasKey('organizers', $stats);
    }

    public function test_get_workshops_with_filters()
    {
        $workshop1 = Workshop::factory()->create([
            'name' => 'Active Workshop',
            'status' => 'published',
            'created_by' => $this->user->id,
        ]);
        $workshop2 = Workshop::factory()->create([
            'name' => 'Draft Workshop',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        // Test without filters
        $allWorkshops = $this->workshopService->getWorkshops();
        $this->assertCount(2, $allWorkshops);

        // Test with status filter
        $publishedWorkshops = $this->workshopService->getWorkshops(['status' => 'published']);
        $this->assertCount(1, $publishedWorkshops);
        $this->assertEquals($workshop1->id, $publishedWorkshops->first()->id);

        // Test with search filter
        $searchedWorkshops = $this->workshopService->getWorkshops(['search' => 'Active']);
        $this->assertCount(1, $searchedWorkshops);
        $this->assertEquals($workshop1->id, $searchedWorkshops->first()->id);
    }

    public function test_get_user_workshops()
    {
        $otherUser = User::factory()->create();
        
        // Workshop created by user
        $createdWorkshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        // Workshop organized by user
        $organizedWorkshop = Workshop::factory()->create(['created_by' => $otherUser->id]);
        $organizedWorkshop->organizers()->attach($this->user->id);
        
        // Workshop not related to user
        Workshop::factory()->create(['created_by' => $otherUser->id]);

        $userWorkshops = $this->workshopService->getUserWorkshops($this->user);

        $this->assertCount(2, $userWorkshops);
        $this->assertTrue($userWorkshops->contains($createdWorkshop));
        $this->assertTrue($userWorkshops->contains($organizedWorkshop));
    }

    public function test_duplicate_workshop()
    {
        $originalWorkshop = Workshop::factory()->create([
            'name' => 'Original Workshop',
            'created_by' => $this->user->id,
        ]);
        
        $ticketType = TicketType::factory()->create(['workshop_id' => $originalWorkshop->id]);

        $overrides = [
            'name' => 'Duplicated Workshop',
            'start_date' => '2024-12-15 09:00:00',
            'end_date' => '2024-12-15 17:00:00',
        ];

        $duplicatedWorkshop = $this->workshopService->duplicateWorkshop($originalWorkshop, $overrides);

        $this->assertNotEquals($originalWorkshop->id, $duplicatedWorkshop->id);
        $this->assertEquals($overrides['name'], $duplicatedWorkshop->name);
        $this->assertEquals($originalWorkshop->description, $duplicatedWorkshop->description);
        $this->assertEquals($originalWorkshop->location, $duplicatedWorkshop->location);
        $this->assertCount(1, $duplicatedWorkshop->ticketTypes);
    }

    public function test_update_workshop_status()
    {
        $workshop = Workshop::factory()->create([
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $updatedWorkshop = $this->workshopService->updateWorkshopStatus($workshop, 'published');

        $this->assertEquals('published', $updatedWorkshop->status);
        $this->assertDatabaseHas('workshops', [
            'id' => $workshop->id,
            'status' => 'published',
        ]);
    }

    public function test_update_workshop_status_with_invalid_status()
    {
        $workshop = Workshop::factory()->create([
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Invalid status/');

        $this->workshopService->updateWorkshopStatus($workshop, 'invalid_status');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
