<?php

namespace Tests\Unit\Models;

use App\Models\EmailTemplate;
use App\Models\Participant;
use App\Models\TicketType;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_creator()
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $workshop->creator);
        $this->assertEquals($user->id, $workshop->creator->id);
    }

    /** @test */
    public function it_has_many_organizers()
    {
        $workshop = Workshop::factory()->create();
        $organizers = User::factory()->count(3)->create();
        
        $workshop->organizers()->attach($organizers->pluck('id'));

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $workshop->organizers);
        $this->assertEquals(3, $workshop->organizers->count());
        foreach ($organizers as $organizer) {
            $this->assertTrue($workshop->organizers->contains($organizer));
        }
    }

    /** @test */
    public function it_has_many_ticket_types()
    {
        $workshop = Workshop::factory()->create();
        $ticketTypes = TicketType::factory()->count(3)->create(['workshop_id' => $workshop->id]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $workshop->ticketTypes);
        $this->assertEquals(3, $workshop->ticketTypes->count());
        foreach ($ticketTypes as $ticketType) {
            $this->assertTrue($workshop->ticketTypes->contains($ticketType));
        }
    }

    /** @test */
    public function it_has_many_participants()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participants = Participant::factory()->count(3)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $workshop->participants);
        $this->assertEquals(3, $workshop->participants->count());
        foreach ($participants as $participant) {
            $this->assertTrue($workshop->participants->contains($participant));
        }
    }

    /** @test */
    public function it_has_many_email_templates()
    {
        $workshop = Workshop::factory()->create();
        $emailTemplates = collect([
            EmailTemplate::factory()->create(['workshop_id' => $workshop->id, 'type' => 'ticket']),
            EmailTemplate::factory()->create(['workshop_id' => $workshop->id, 'type' => 'invite']),
            EmailTemplate::factory()->create(['workshop_id' => $workshop->id, 'type' => 'confirm'])
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $workshop->emailTemplates);
        $this->assertEquals(3, $workshop->emailTemplates->count());
        foreach ($emailTemplates as $template) {
            $this->assertTrue($workshop->emailTemplates->contains($template));
        }
    }

    /** @test */
    public function by_status_scope_filters_workshops_by_status()
    {
        Workshop::factory()->create(['status' => 'draft']);
        Workshop::factory()->create(['status' => 'published']);
        Workshop::factory()->create(['status' => 'draft']);

        $draftWorkshops = Workshop::byStatus('draft')->get();
        $publishedWorkshops = Workshop::byStatus('published')->get();

        $this->assertEquals(2, $draftWorkshops->count());
        $this->assertEquals(1, $publishedWorkshops->count());
        
        foreach ($draftWorkshops as $workshop) {
            $this->assertEquals('draft', $workshop->status);
        }
        
        foreach ($publishedWorkshops as $workshop) {
            $this->assertEquals('published', $workshop->status);
        }
    }

    /** @test */
    public function upcoming_scope_returns_future_workshops()
    {
        Workshop::factory()->create(['start_date' => now()->addDays(5)]);
        Workshop::factory()->create(['start_date' => now()->subDays(5)]);
        Workshop::factory()->create(['start_date' => now()->addDays(10)]);

        $upcomingWorkshops = Workshop::upcoming()->get();

        $this->assertEquals(2, $upcomingWorkshops->count());
        foreach ($upcomingWorkshops as $workshop) {
            $this->assertTrue($workshop->start_date->isFuture());
        }
    }

    /** @test */
    public function ongoing_scope_returns_current_workshops()
    {
        Workshop::factory()->create([
            'start_date' => now()->subHours(2),
            'end_date' => now()->addHours(2)
        ]);
        Workshop::factory()->create([
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2)
        ]);
        Workshop::factory()->create([
            'start_date' => now()->subDays(2),
            'end_date' => now()->subDays(1)
        ]);

        $ongoingWorkshops = Workshop::ongoing()->get();

        $this->assertEquals(1, $ongoingWorkshops->count());
        $workshop = $ongoingWorkshops->first();
        $this->assertTrue($workshop->start_date->isPast());
        $this->assertTrue($workshop->end_date->isFuture());
    }

    /** @test */
    public function completed_scope_returns_past_workshops()
    {
        Workshop::factory()->create(['end_date' => now()->subDays(1)]);
        Workshop::factory()->create(['end_date' => now()->addDays(1)]);
        Workshop::factory()->create(['end_date' => now()->subDays(5)]);

        $completedWorkshops = Workshop::completed()->get();

        $this->assertEquals(2, $completedWorkshops->count());
        foreach ($completedWorkshops as $workshop) {
            $this->assertTrue($workshop->end_date->isPast());
        }
    }

    /** @test */
    public function it_casts_dates_properly()
    {
        $workshop = Workshop::factory()->create([
            'start_date' => '2024-01-15 09:00:00',
            'end_date' => '2024-01-15 17:00:00'
        ]);

        $this->assertInstanceOf('Illuminate\Support\Carbon', $workshop->start_date);
        $this->assertInstanceOf('Illuminate\Support\Carbon', $workshop->end_date);
    }

    /** @test */
    public function deleting_workshop_cascades_to_related_models()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id
        ]);
        $emailTemplate = EmailTemplate::factory()->create(['workshop_id' => $workshop->id]);

        $workshopId = $workshop->id;
        $workshop->delete();

        $this->assertDatabaseMissing('workshops', ['id' => $workshopId]);
        $this->assertDatabaseMissing('ticket_types', ['workshop_id' => $workshopId]);
        $this->assertDatabaseMissing('participants', ['workshop_id' => $workshopId]);
        $this->assertDatabaseMissing('email_templates', ['workshop_id' => $workshopId]);
    }
}