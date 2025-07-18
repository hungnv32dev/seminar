<?php

namespace Tests\Unit\Models;

use App\Models\Participant;
use App\Models\TicketType;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTypeModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_workshop()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);

        $this->assertInstanceOf(Workshop::class, $ticketType->workshop);
        $this->assertEquals($workshop->id, $ticketType->workshop->id);
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

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $ticketType->participants);
        $this->assertEquals(3, $ticketType->participants->count());
        foreach ($participants as $participant) {
            $this->assertTrue($ticketType->participants->contains($participant));
        }
    }

    /** @test */
    public function participant_count_attribute_returns_correct_count()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        Participant::factory()->count(5)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id
        ]);

        $this->assertEquals(5, $ticketType->participant_count);
    }

    /** @test */
    public function total_revenue_attribute_calculates_correctly()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create([
            'workshop_id' => $workshop->id,
            'price' => 100.00
        ]);
        
        // Create 3 paid participants and 2 unpaid
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

        $this->assertEquals(300.00, $ticketType->total_revenue);
    }

    /** @test */
    public function total_revenue_attribute_returns_zero_for_no_paid_participants()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create([
            'workshop_id' => $workshop->id,
            'price' => 50.00
        ]);
        
        // Create only unpaid participants
        Participant::factory()->count(3)->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id,
            'is_paid' => false
        ]);

        $this->assertEquals(0.00, $ticketType->total_revenue);
    }

    /** @test */
    public function for_workshop_scope_filters_by_workshop()
    {
        $workshop1 = Workshop::factory()->create();
        $workshop2 = Workshop::factory()->create();
        
        $ticketType1 = TicketType::factory()->create(['workshop_id' => $workshop1->id]);
        $ticketType2 = TicketType::factory()->create(['workshop_id' => $workshop2->id]);
        $ticketType3 = TicketType::factory()->create(['workshop_id' => $workshop1->id]);

        $workshop1TicketTypes = TicketType::forWorkshop($workshop1->id)->get();
        $workshop2TicketTypes = TicketType::forWorkshop($workshop2->id)->get();

        $this->assertEquals(2, $workshop1TicketTypes->count());
        $this->assertEquals(1, $workshop2TicketTypes->count());
        
        $this->assertTrue($workshop1TicketTypes->contains($ticketType1));
        $this->assertTrue($workshop1TicketTypes->contains($ticketType3));
        $this->assertTrue($workshop2TicketTypes->contains($ticketType2));
    }

    /** @test */
    public function it_casts_price_to_decimal()
    {
        $ticketType = TicketType::factory()->create(['price' => 99.99]);

        // Laravel's decimal casting returns a string, not float
        $this->assertIsString($ticketType->price);
        $this->assertEquals('99.99', $ticketType->price);
    }

    /** @test */
    public function deleting_ticket_type_cascades_to_participants()
    {
        $workshop = Workshop::factory()->create();
        $ticketType = TicketType::factory()->create(['workshop_id' => $workshop->id]);
        $participant = Participant::factory()->create([
            'workshop_id' => $workshop->id,
            'ticket_type_id' => $ticketType->id
        ]);

        $ticketTypeId = $ticketType->id;
        $ticketType->delete();

        $this->assertDatabaseMissing('ticket_types', ['id' => $ticketTypeId]);
        $this->assertDatabaseMissing('participants', ['ticket_type_id' => $ticketTypeId]);
    }

    /** @test */
    public function it_maintains_foreign_key_constraint_with_workshop()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        // Try to create ticket type with non-existent workshop
        TicketType::factory()->create(['workshop_id' => 999999]);
    }
}