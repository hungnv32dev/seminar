<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Workshop;
use App\Models\TicketType;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TicketTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'view ticket types']);
        Permission::create(['name' => 'create ticket types']);
        Permission::create(['name' => 'edit ticket types']);
        Permission::create(['name' => 'delete ticket types']);
        
        // Create role
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view ticket types', 'create ticket types', 'edit ticket types', 'delete ticket types'
        ]);
        
        // Create user and workshop
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        
        $this->workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
    }

    public function test_index_displays_ticket_types()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('ticket-types.index');
        $response->assertViewHas('ticketTypes');
        $response->assertSee($ticketType->name);
    }

    public function test_index_requires_authentication()
    {
        $response = $this->get(route('ticket-types.index'));
        
        $response->assertRedirect(route('login'));
    }

    public function test_index_filters_by_workshop()
    {
        $otherWorkshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $ticketType1 = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        $ticketType2 = TicketType::factory()->create(['workshop_id' => $otherWorkshop->id]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.index', ['workshop_id' => $this->workshop->id]));
        
        $response->assertStatus(200);
        $response->assertSee($ticketType1->name);
        $response->assertDontSee($ticketType2->name);
    }

    public function test_create_displays_form()
    {
        $response = $this->actingAs($this->user)->get(route('ticket-types.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('ticket-types.create');
        $response->assertViewHas('workshops');
    }

    public function test_create_with_workshop_preselected()
    {
        $response = $this->actingAs($this->user)->get(route('ticket-types.create', ['workshop_id' => $this->workshop->id]));
        
        $response->assertStatus(200);
        $response->assertViewHas('workshop');
    }

    public function test_store_creates_ticket_type_with_valid_data()
    {
        $ticketTypeData = [
            'workshop_id' => $this->workshop->id,
            'name' => 'Early Bird',
            'price' => 99.99,
        ];

        $response = $this->actingAs($this->user)->post(route('ticket-types.store'), $ticketTypeData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ticket_types', [
            'workshop_id' => $this->workshop->id,
            'name' => 'Early Bird',
            'price' => 99.99,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('ticket-types.store'), []);

        $response->assertSessionHasErrors(['workshop_id', 'name', 'price']);
    }

    public function test_store_validates_price_format()
    {
        $ticketTypeData = [
            'workshop_id' => $this->workshop->id,
            'name' => 'Test Ticket',
            'price' => 'invalid-price',
        ];

        $response = $this->actingAs($this->user)->post(route('ticket-types.store'), $ticketTypeData);

        $response->assertSessionHasErrors(['price']);
    }

    public function test_store_validates_negative_price()
    {
        $ticketTypeData = [
            'workshop_id' => $this->workshop->id,
            'name' => 'Test Ticket',
            'price' => -10.00,
        ];

        $response = $this->actingAs($this->user)->post(route('ticket-types.store'), $ticketTypeData);

        $response->assertSessionHasErrors(['price']);
    }

    public function test_show_displays_ticket_type()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.show', $ticketType));
        
        $response->assertStatus(200);
        $response->assertViewIs('ticket-types.show');
        $response->assertViewHas(['ticketType', 'stats']);
        $response->assertSee($ticketType->name);
    }

    public function test_edit_displays_form()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.edit', $ticketType));
        
        $response->assertStatus(200);
        $response->assertViewIs('ticket-types.edit');
        $response->assertViewHas(['ticketType', 'workshops']);
    }

    public function test_update_modifies_ticket_type_with_valid_data()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        $updateData = [
            'workshop_id' => $this->workshop->id,
            'name' => 'Updated Ticket Type',
            'price' => 149.99,
        ];

        $response = $this->actingAs($this->user)->put(route('ticket-types.update', $ticketType), $updateData);

        $response->assertRedirect(route('ticket-types.show', $ticketType));
        $this->assertDatabaseHas('ticket_types', [
            'id' => $ticketType->id,
            'name' => 'Updated Ticket Type',
            'price' => 149.99,
        ]);
    }

    public function test_update_validates_required_fields()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        $response = $this->actingAs($this->user)->put(route('ticket-types.update', $ticketType), [
            'name' => '',
            'price' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'price']);
    }

    public function test_destroy_deletes_ticket_type_without_participants()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        $response = $this->actingAs($this->user)->delete(route('ticket-types.destroy', $ticketType));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('ticket_types', ['id' => $ticketType->id]);
    }

    public function test_destroy_prevents_deletion_with_participants()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->delete(route('ticket-types.destroy', $ticketType));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('ticket_types', ['id' => $ticketType->id]);
    }

    public function test_destroy_prevents_deletion_of_last_ticket_type()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        // This is the only ticket type for the workshop
        
        $response = $this->actingAs($this->user)->delete(route('ticket-types.destroy', $ticketType));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('ticket_types', ['id' => $ticketType->id]);
    }

    public function test_get_by_workshop_returns_json()
    {
        $ticketType1 = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        $ticketType2 = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.by-workshop', $this->workshop));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'ticket_types' => [
                '*' => [
                    'id',
                    'name',
                    'price',
                    'price_formatted',
                    'participants_count',
                    'paid_participants_count',
                    'revenue',
                ]
            ]
        ]);
    }

    public function test_statistics_returns_json()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => true,
            'is_checked_in' => true,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.statistics', $ticketType));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'price',
            'price_formatted',
            'total_participants',
            'paid_participants',
            'unpaid_participants',
            'checked_in_participants',
            'not_checked_in_participants',
            'total_revenue',
            'potential_revenue',
            'payment_rate',
            'check_in_rate',
        ]);
    }

    public function test_duplicate_creates_copy_of_ticket_type()
    {
        $ticketType = TicketType::factory()->create([
            'workshop_id' => $this->workshop->id,
            'name' => 'Original Ticket',
            'price' => 100.00,
        ]);
        
        $response = $this->actingAs($this->user)->post(route('ticket-types.duplicate', $ticketType));
        
        $response->assertRedirect();
        $this->assertDatabaseCount('ticket_types', 2);
        
        $duplicatedTicketType = TicketType::where('id', '!=', $ticketType->id)->first();
        $this->assertStringContainsString('Copy', $duplicatedTicketType->name);
        $this->assertEquals($ticketType->price, $duplicatedTicketType->price);
        $this->assertEquals($ticketType->workshop_id, $duplicatedTicketType->workshop_id);
    }

    public function test_check_deletion_returns_json()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.check-deletion', $ticketType));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'can_delete',
            'reasons',
            'participant_count',
            'workshop_ticket_type_count',
        ]);
    }

    public function test_check_deletion_with_participants()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.check-deletion', $ticketType));
        
        $response->assertStatus(200);
        $response->assertJson([
            'can_delete' => false,
        ]);
        
        $data = $response->json();
        $this->assertGreaterThan(0, count($data['reasons']));
    }

    public function test_check_deletion_last_ticket_type()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        // This is the only ticket type
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.check-deletion', $ticketType));
        
        $response->assertStatus(200);
        $response->assertJson([
            'can_delete' => false,
        ]);
        
        $data = $response->json();
        $this->assertContains('This is the only ticket type for the workshop.', $data['reasons']);
    }

    public function test_check_deletion_can_delete()
    {
        // Create two ticket types so we can delete one
        $ticketType1 = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        $ticketType2 = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.check-deletion', $ticketType1));
        
        $response->assertStatus(200);
        $response->assertJson([
            'can_delete' => true,
        ]);
    }

    public function test_unauthorized_user_cannot_access_ticket_types()
    {
        $unauthorizedUser = User::factory()->create();
        
        $response = $this->actingAs($unauthorizedUser)->get(route('ticket-types.index'));
        
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_create_ticket_type()
    {
        $unauthorizedUser = User::factory()->create();
        
        $response = $this->actingAs($unauthorizedUser)->get(route('ticket-types.create'));
        
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_edit_ticket_type()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        $unauthorizedUser = User::factory()->create();
        
        $response = $this->actingAs($unauthorizedUser)->get(route('ticket-types.edit', $ticketType));
        
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_delete_ticket_type()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        $unauthorizedUser = User::factory()->create();
        
        $response = $this->actingAs($unauthorizedUser)->delete(route('ticket-types.destroy', $ticketType));
        
        $response->assertStatus(403);
    }

    public function test_store_allows_free_ticket_type()
    {
        $ticketTypeData = [
            'workshop_id' => $this->workshop->id,
            'name' => 'Free Ticket',
            'price' => 0.00,
        ];

        $response = $this->actingAs($this->user)->post(route('ticket-types.store'), $ticketTypeData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ticket_types', [
            'workshop_id' => $this->workshop->id,
            'name' => 'Free Ticket',
            'price' => 0.00,
        ]);
    }

    public function test_show_displays_statistics_correctly()
    {
        $ticketType = TicketType::factory()->create([
            'workshop_id' => $this->workshop->id,
            'price' => 100.00,
        ]);
        
        // Create participants with different statuses
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => true,
            'is_checked_in' => true,
        ]);
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => false,
            'is_checked_in' => false,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.show', $ticketType));
        
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        $this->assertEquals(2, $stats['total_participants']);
        $this->assertEquals(1, $stats['paid_participants']);
        $this->assertEquals(1, $stats['checked_in_participants']);
        $this->assertEquals(100.00, $stats['total_revenue']);
    }

    public function test_index_displays_participant_counts()
    {
        $ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        // Create participants
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => true,
        ]);
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => false,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('ticket-types.index'));
        
        $response->assertStatus(200);
        $response->assertSee('2'); // Total participants
        $response->assertSee('1'); // Paid participants (in the progress display)
    }
}