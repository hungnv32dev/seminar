<?php

namespace Tests\Unit\Models;

use App\Models\Participant;
use App\Models\TicketType;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_workshop()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id
        ]);

        $this->assertInstanceOf(Workshop::class, $participant->workshop);
        $this->assertEquals($workshop->id, $participant->workshop->id);
    }

    /** @test */
    public function it_belongs_to_ticket_type()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id
        ]);

        $this->assertInstanceOf(TicketType::class, $participant->ticketType);
        $this->assertEquals($ticketType->id, $participant->ticketType->id);
    }

    /** @test */
    public function it_generates_ticket_code_on_creation()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'ticket_code' => null // Explicitly set to null to test auto-generation
        ]);

        $this->assertNotNull($participant->ticket_code);
        $this->assertEquals(8, strlen($participant->ticket_code));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $participant->ticket_code);
    }

    /** @test */
    public function it_generates_unique_ticket_codes()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        
        $participants = Participant::factory()->count(10)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id
        ]);

        $ticketCodes = $participants->pluck('ticket_code')->toArray();
        $uniqueCodes = array_unique($ticketCodes);

        $this->assertEquals(count($ticketCodes), count($uniqueCodes));
    }

    /** @test */
    public function generate_ticket_code_method_returns_unique_code()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = new Participant([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id
        ]);

        $code1 = $participant->generateTicketCode();
        $code2 = $participant->generateTicketCode();

        $this->assertNotEquals($code1, $code2);
        $this->assertEquals(8, strlen($code1));
        $this->assertEquals(8, strlen($code2));
    }

    /** @test */
    public function get_qr_code_url_returns_correct_route()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'ticket_code' => 'TEST1234'
        ]);

        $expectedUrl = route('qr-code.generate', ['ticket_code' => 'TEST1234']);
        $this->assertEquals($expectedUrl, $participant->getQrCodeUrl());
    }

    /** @test */
    public function check_in_method_updates_status_and_timestamp()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_checked_in' => false,
            'checked_in_at' => null
        ]);

        $participant->checkIn();

        $this->assertTrue($participant->fresh()->is_checked_in);
        $this->assertNotNull($participant->fresh()->checked_in_at);
        $this->assertInstanceOf('Illuminate\Support\Carbon', $participant->fresh()->checked_in_at);
    }

    /** @test */
    public function paid_scope_returns_only_paid_participants()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        
        Participant::factory()->count(3)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => true
        ]);
        Participant::factory()->count(2)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => false
        ]);

        $paidParticipants = Participant::paid()->get();

        $this->assertEquals(3, $paidParticipants->count());
        foreach ($paidParticipants as $participant) {
            $this->assertTrue($participant->is_paid);
        }
    }

    /** @test */
    public function unpaid_scope_returns_only_unpaid_participants()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        
        Participant::factory()->count(2)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => true
        ]);
        Participant::factory()->count(3)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => false
        ]);

        $unpaidParticipants = Participant::unpaid()->get();

        $this->assertEquals(3, $unpaidParticipants->count());
        foreach ($unpaidParticipants as $participant) {
            $this->assertFalse($participant->is_paid);
        }
    }

    /** @test */
    public function checked_in_scope_returns_only_checked_in_participants()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        
        Participant::factory()->count(2)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_checked_in' => true
        ]);
        Participant::factory()->count(3)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_checked_in' => false
        ]);

        $checkedInParticipants = Participant::checkedIn()->get();

        $this->assertEquals(2, $checkedInParticipants->count());
        foreach ($checkedInParticipants as $participant) {
            $this->assertTrue($participant->is_checked_in);
        }
    }

    /** @test */
    public function not_checked_in_scope_returns_only_not_checked_in_participants()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        
        Participant::factory()->count(2)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_checked_in' => true
        ]);
        Participant::factory()->count(4)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_checked_in' => false
        ]);

        $notCheckedInParticipants = Participant::notCheckedIn()->get();

        $this->assertEquals(4, $notCheckedInParticipants->count());
        foreach ($notCheckedInParticipants as $participant) {
            $this->assertFalse($participant->is_checked_in);
        }
    }

    /** @test */
    public function for_workshop_scope_filters_by_workshop()
    {
        $workshop1 = Workshop::factory()->create();
        $workshop2 = Workshop::factory()->create();
        $ticketType1 = TicketType::factory()->create(['workshop_id' => $workshop1->id]);
        $ticketType2 = TicketType::factory()->create(['workshop_id' => $workshop2->id]);
        
        Participant::factory()->count(3)->create([
            'workshop_id' => $workshop1->id,
            'ticket_type_id' => $ticketType1->id
        ]);
        Participant::factory()->count(2)->create([
            'workshop_id' => $workshop2->id,
            'ticket_type_id' => $ticketType2->id
        ]);

        $workshop1Participants = Participant::forWorkshop($workshop1->id)->get();
        $workshop2Participants = Participant::forWorkshop($workshop2->id)->get();

        $this->assertEquals(3, $workshop1Participants->count());
        $this->assertEquals(2, $workshop2Participants->count());
        
        foreach ($workshop1Participants as $participant) {
            $this->assertEquals($workshop1->id, $participant->workshop_id);
        }
        
        foreach ($workshop2Participants as $participant) {
            $this->assertEquals($workshop2->id, $participant->workshop_id);
        }
    }

    /** @test */
    public function it_casts_boolean_fields_correctly()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => 1,
            'is_checked_in' => 0
        ]);

        $this->assertIsBool($participant->is_paid);
        $this->assertIsBool($participant->is_checked_in);
        $this->assertTrue($participant->is_paid);
        $this->assertFalse($participant->is_checked_in);
    }

    /** @test */
    public function it_casts_checked_in_at_to_carbon()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'checked_in_at' => '2024-01-15 10:30:00'
        ]);

        $this->assertInstanceOf('Illuminate\Support\Carbon', $participant->checked_in_at);
    }

    /** @test */
    public function it_maintains_foreign_key_constraints()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        // Try to create participant with non-existent workshop
        Participant::factory()->create([
            'workshop_id' => 999999,
            'ticket_type_id' => 1
        ]);
    }
}