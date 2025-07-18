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

class WorkshopControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'view workshops']);
        Permission::create(['name' => 'create workshops']);
        Permission::create(['name' => 'edit workshops']);
        Permission::create(['name' => 'delete workshops']);
        Permission::create(['name' => 'manage workshops']);
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        
        // Assign permissions to admin
        $adminRole->givePermissionTo([
            'view workshops', 'create workshops', 'edit workshops', 
            'delete workshops', 'manage workshops'
        ]);
        
        // Assign basic permissions to user
        $userRole->givePermissionTo(['view workshops', 'create workshops']);
        
        // Create users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
    }

    public function test_index_displays_workshops_for_authenticated_user()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->get(route('workshops.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('workshops.index');
        $response->assertViewHas('workshops');
        $response->assertSee($workshop->name);
    }

    public function test_index_requires_authentication()
    {
        $response = $this->get(route('workshops.index'));
        
        $response->assertRedirect(route('login'));
    }

    public function test_create_displays_form_for_authorized_user()
    {
        $response = $this->actingAs($this->user)->get(route('workshops.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('workshops.create');
        $response->assertViewHas('organizers');
    }

    public function test_store_creates_workshop_with_valid_data()
    {
        $workshopData = [
            'name' => 'Test Workshop',
            'description' => 'Test Description',
            'start_date' => '2024-12-01 09:00:00',
            'end_date' => '2024-12-01 17:00:00',
            'location' => 'Test Location',
            'status' => 'draft',
            'max_participants' => 50,
        ];

        $response = $this->actingAs($this->user)->post(route('workshops.store'), $workshopData);

        $response->assertRedirect();
        $this->assertDatabaseHas('workshops', [
            'name' => 'Test Workshop',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('workshops.store'), []);

        $response->assertSessionHasErrors(['name', 'start_date', 'end_date', 'location']);
    }

    public function test_store_validates_date_order()
    {
        $workshopData = [
            'name' => 'Test Workshop',
            'start_date' => '2024-12-01 17:00:00',
            'end_date' => '2024-12-01 09:00:00', // End before start
            'location' => 'Test Location',
        ];

        $response = $this->actingAs($this->user)->post(route('workshops.store'), $workshopData);

        $response->assertSessionHasErrors(['end_date']);
    }

    public function test_show_displays_workshop_for_owner()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->get(route('workshops.show', $workshop));
        
        $response->assertStatus(200);
        $response->assertViewIs('workshops.show');
        $response->assertViewHas('workshop');
        $response->assertSee($workshop->name);
    }

    public function test_show_displays_workshop_for_admin()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($this->admin)->get(route('workshops.show', $workshop));
        
        $response->assertStatus(200);
        $response->assertSee($workshop->name);
    }

    public function test_show_denies_access_to_unauthorized_user()
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('user');
        
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($otherUser)->get(route('workshops.show', $workshop));
        
        $response->assertStatus(403);
    }

    public function test_edit_displays_form_for_owner()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->get(route('workshops.edit', $workshop));
        
        $response->assertStatus(200);
        $response->assertViewIs('workshops.edit');
        $response->assertViewHas('workshop');
    }

    public function test_update_modifies_workshop_with_valid_data()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $updateData = [
            'name' => 'Updated Workshop Name',
            'description' => 'Updated Description',
            'start_date' => $workshop->start_date->format('Y-m-d H:i:s'),
            'end_date' => $workshop->end_date->format('Y-m-d H:i:s'),
            'location' => 'Updated Location',
            'status' => 'published',
        ];

        $response = $this->actingAs($this->user)->put(route('workshops.update', $workshop), $updateData);

        $response->assertRedirect(route('workshops.show', $workshop));
        $this->assertDatabaseHas('workshops', [
            'id' => $workshop->id,
            'name' => 'Updated Workshop Name',
            'location' => 'Updated Location',
        ]);
    }

    public function test_update_validates_required_fields()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->put(route('workshops.update', $workshop), [
            'name' => '',
            'location' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'location']);
    }

    public function test_destroy_deletes_workshop_without_participants()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->delete(route('workshops.destroy', $workshop));
        
        $response->assertRedirect(route('workshops.index'));
        $this->assertDatabaseMissing('workshops', ['id' => $workshop->id]);
    }

    public function test_destroy_prevents_deletion_with_participants()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->delete(route('workshops.destroy', $workshop));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('workshops', ['id' => $workshop->id]);
    }

    public function test_duplicate_creates_copy_of_workshop()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        
        $response = $this->actingAs($this->user)->post(route('workshops.duplicate', $workshop));
        
        $response->assertRedirect();
        $this->assertDatabaseCount('workshops', 2);
        
        $duplicatedWorkshop = Workshop::where('id', '!=', $workshop->id)->first();
        $this->assertStringContainsString('Copy', $duplicatedWorkshop->name);
        $this->assertEquals($this->user->id, $duplicatedWorkshop->created_by);
    }

    public function test_statistics_returns_json_for_ajax()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => true,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('workshops.statistics', $workshop));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'workshop_id',
            'workshop_name',
            'total_participants',
            'paid_participants',
            'checked_in_participants',
            'total_revenue',
        ]);
    }

    public function test_participants_returns_json_for_ajax()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('workshops.participants', $workshop));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'participants' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'ticket_code',
                    'ticket_type',
                    'is_paid',
                    'is_checked_in',
                ]
            ]
        ]);
    }

    public function test_unauthorized_user_cannot_create_workshop()
    {
        $unauthorizedUser = User::factory()->create();
        // Don't assign any role or permissions
        
        $response = $this->actingAs($unauthorizedUser)->get(route('workshops.create'));
        
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_edit_workshop()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $unauthorizedUser = User::factory()->create();
        
        $response = $this->actingAs($unauthorizedUser)->get(route('workshops.edit', $workshop));
        
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_delete_workshop()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $unauthorizedUser = User::factory()->create();
        
        $response = $this->actingAs($unauthorizedUser)->delete(route('workshops.destroy', $workshop));
        
        $response->assertStatus(403);
    }

    public function test_workshop_with_organizers()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('user');
        
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $workshop->organizers()->attach($organizer->id);
        
        // Organizer should be able to view the workshop
        $response = $this->actingAs($organizer)->get(route('workshops.show', $workshop));
        $response->assertStatus(200);
        
        // Organizer should be able to edit the workshop
        $response = $this->actingAs($organizer)->get(route('workshops.edit', $workshop));
        $response->assertStatus(200);
    }

    public function test_index_filters_workshops_by_status()
    {
        $publishedWorkshop = Workshop::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'published'
        ]);
        $draftWorkshop = Workshop::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'draft'
        ]);
        
        $response = $this->actingAs($this->user)->get(route('workshops.index', ['status' => 'published']));
        
        $response->assertStatus(200);
        $response->assertSee($publishedWorkshop->name);
        $response->assertDontSee($draftWorkshop->name);
    }

    public function test_index_searches_workshops_by_name()
    {
        $workshop1 = Workshop::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Laravel Workshop'
        ]);
        $workshop2 = Workshop::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Vue.js Workshop'
        ]);
        
        $response = $this->actingAs($this->user)->get(route('workshops.index', ['search' => 'Laravel']));
        
        $response->assertStatus(200);
        $response->assertSee($workshop1->name);
        $response->assertDontSee($workshop2->name);
    }

    public function test_admin_can_see_all_workshops()
    {
        $userWorkshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $otherUserWorkshop = Workshop::factory()->create(['created_by' => User::factory()->create()->id]);
        
        $response = $this->actingAs($this->admin)->get(route('workshops.index'));
        
        $response->assertStatus(200);
        $response->assertSee($userWorkshop->name);
        $response->assertSee($otherUserWorkshop->name);
    }

    public function test_regular_user_only_sees_own_workshops()
    {
        $userWorkshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $otherUserWorkshop = Workshop::factory()->create(['created_by' => User::factory()->create()->id]);
        
        $response = $this->actingAs($this->user)->get(route('workshops.index'));
        
        $response->assertStatus(200);
        $response->assertSee($userWorkshop->name);
        $response->assertDontSee($otherUserWorkshop->name);
    }
}