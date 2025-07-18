<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Workshop;
use App\Models\TicketType;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ParticipantControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workshop $workshop;
    protected TicketType $ticketType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'view participants']);
        Permission::create(['name' => 'create participants']);
        Permission::create(['name' => 'edit participants']);
        Permission::create(['name' => 'delete participants']);
        Permission::create(['name' => 'import participants']);
        
        // Create role
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view participants', 'create participants', 'edit participants', 
            'delete participants', 'import participants'
        ]);
        
        // Create user and workshop
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        
        $this->workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $this->ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        
        Mail::fake();
    }

    public function test_index_displays_participants()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('participants.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('participants.index');
        $response->assertViewHas('participants');
        $response->assertSee($participant->name);
    }

    public function test_index_requires_authentication()
    {
        $response = $this->get(route('participants.index'));
        
        $response->assertRedirect(route('login'));
    }

    public function test_create_displays_form()
    {
        $response = $this->actingAs($this->user)->get(route('participants.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('participants.create');
        $response->assertViewHas(['workshops', 'ticketTypes']);
    }

    public function test_store_creates_participant_with_valid_data()
    {
        $participantData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'company' => 'Test Company',
            'position' => 'Developer',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => true,
        ];

        $response = $this->actingAs($this->user)->post(route('participants.store'), $participantData);

        $response->assertRedirect();
        $this->assertDatabaseHas('participants', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'workshop_id' => $this->workshop->id,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('participants.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'workshop_id', 'ticket_type_id']);
    }

    public function test_store_validates_email_format()
    {
        $participantData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ];

        $response = $this->actingAs($this->user)->post(route('participants.store'), $participantData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_store_prevents_duplicate_email_in_same_workshop()
    {
        Participant::factory()->create([
            'email' => 'john@example.com',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);

        $participantData = [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ];

        $response = $this->actingAs($this->user)->post(route('participants.store'), $participantData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_show_displays_participant()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('participants.show', $participant));
        
        $response->assertStatus(200);
        $response->assertViewIs('participants.show');
        $response->assertViewHas('participant');
        $response->assertSee($participant->name);
    }

    public function test_edit_displays_form()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('participants.edit', $participant));
        
        $response->assertStatus(200);
        $response->assertViewIs('participants.edit');
        $response->assertViewHas(['participant', 'workshops', 'ticketTypes']);
    }

    public function test_update_modifies_participant_with_valid_data()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '+9876543210',
            'company' => 'Updated Company',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ];

        $response = $this->actingAs($this->user)->put(route('participants.update', $participant), $updateData);

        $response->assertRedirect(route('participants.show', $participant));
        $this->assertDatabaseHas('participants', [
            'id' => $participant->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_destroy_deletes_participant()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->delete(route('participants.destroy', $participant));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('participants', ['id' => $participant->id]);
    }

    public function test_import_displays_form()
    {
        $response = $this->actingAs($this->user)->get(route('participants.import'));
        
        $response->assertStatus(200);
        $response->assertViewIs('participants.import');
        $response->assertViewHas(['workshops', 'ticketTypes']);
    }

    public function test_process_import_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('participants.process-import'), []);

        $response->assertSessionHasErrors(['workshop_id', 'default_ticket_type_id', 'excel_file']);
    }

    public function test_process_import_validates_file_type()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('participants.txt', 100);

        $response = $this->actingAs($this->user)->post(route('participants.process-import'), [
            'workshop_id' => $this->workshop->id,
            'default_ticket_type_id' => $this->ticketType->id,
            'excel_file' => $file,
        ]);

        $response->assertSessionHasErrors(['excel_file']);
    }

    public function test_resend_ticket_sends_email()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->post(route('participants.resend-ticket', $participant));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_toggle_payment_changes_status()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => false,
        ]);
        
        $response = $this->actingAs($this->user)->post(route('participants.toggle-payment', $participant));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $participant->refresh();
        $this->assertTrue($participant->is_paid);
    }

    public function test_bulk_emails_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('participants.bulk-emails'), []);

        $response->assertSessionHasErrors(['participant_ids', 'email_type']);
    }

    public function test_bulk_emails_sends_to_selected_participants()
    {
        $participant1 = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        $participant2 = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->post(route('participants.bulk-emails'), [
            'participant_ids' => [$participant1->id, $participant2->id],
            'email_type' => 'reminder',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_bulk_payment_updates_multiple_participants()
    {
        $participant1 = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => false,
        ]);
        $participant2 = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => false,
        ]);
        
        $response = $this->actingAs($this->user)->post(route('participants.bulk-payment'), [
            'participant_ids' => [$participant1->id, $participant2->id],
            'is_paid' => true,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $participant1->refresh();
        $participant2->refresh();
        $this->assertTrue($participant1->is_paid);
        $this->assertTrue($participant2->is_paid);
    }

    public function test_get_ticket_types_returns_json()
    {
        $response = $this->actingAs($this->user)->get(route('participants.ticket-types', $this->workshop));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'price']
        ]);
    }

    public function test_get_workshop_stats_returns_json()
    {
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => true,
            'is_checked_in' => true,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('participants.workshop-stats', $this->workshop));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total',
            'paid',
            'unpaid',
            'checked_in',
            'not_checked_in',
        ]);
    }

    public function test_export_returns_json_data()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('participants.export'));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'participants' => [
                '*' => [
                    'name',
                    'email',
                    'workshop',
                    'ticket_type',
                    'ticket_code',
                    'is_paid',
                    'is_checked_in',
                ]
            ]
        ]);
    }

    public function test_index_filters_by_workshop()
    {
        $otherWorkshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $otherTicketType = TicketType::factory()->create(['workshop_id' => $otherWorkshop->id]);
        
        $participant1 = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        $participant2 = Participant::factory()->create([
            'workshop_id' => $otherWorkshop->id,
            'ticket_type_id' => $otherTicketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('participants.index', ['workshop_id' => $this->workshop->id]));
        
        $response->assertStatus(200);
        $response->assertSee($participant1->name);
        $response->assertDontSee($participant2->name);
    }

    public function test_index_filters_by_payment_status()
    {
        $paidParticipant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => true,
        ]);
        $unpaidParticipant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => false,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('participants.index', ['is_paid' => '1']));
        
        $response->assertStatus(200);
        $response->assertSee($paidParticipant->name);
        $response->assertDontSee($unpaidParticipant->name);
    }

    public function test_index_searches_by_name()
    {
        $participant1 = Participant::factory()->create([
            'name' => 'John Doe',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        $participant2 = Participant::factory()->create([
            'name' => 'Jane Smith',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $response = $this->actingAs($this->user)->get(route('participants.index', ['search' => 'John']));
        
        $response->assertStatus(200);
        $response->assertSee($participant1->name);
        $response->assertDontSee($participant2->name);
    }

    public function test_unauthorized_user_cannot_access_participants()
    {
        $unauthorizedUser = User::factory()->create();
        
        $response = $this->actingAs($unauthorizedUser)->get(route('participants.index'));
        
        $response->assertStatus(403);
    }

    public function test_create_with_workshop_preselected()
    {
        $response = $this->actingAs($this->user)->get(route('participants.create', ['workshop_id' => $this->workshop->id]));
        
        $response->assertStatus(200);
        $response->assertViewHas('workshop');
        $response->assertViewHas('ticketTypes');
    }

    public function test_store_generates_unique_ticket_code()
    {
        $participantData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ];

        $response = $this->actingAs($this->user)->post(route('participants.store'), $participantData);

        $response->assertRedirect();
        
        $participant = Participant::where('email', 'john@example.com')->first();
        $this->assertNotNull($participant->ticket_code);
        $this->assertEquals(8, strlen($participant->ticket_code));
    }

    public function test_update_prevents_duplicate_email_in_same_workshop()
    {
        $participant1 = Participant::factory()->create([
            'email' => 'john@example.com',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        
        $participant2 = Participant::factory()->create([
            'email' => 'jane@example.com',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);

        $updateData = [
            'name' => 'Jane Updated',
            'email' => 'john@example.com', // Trying to use existing email
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ];

        $response = $this->actingAs($this->user)->put(route('participants.update', $participant2), $updateData);

        $response->assertSessionHasErrors(['email']);
    }
}