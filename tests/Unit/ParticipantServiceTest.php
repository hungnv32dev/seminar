<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ParticipantService;
use App\Models\Participant;
use App\Models\Workshop;
use App\Models\User;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mockery;

class ParticipantServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ParticipantService $participantService;
    protected Workshop $workshop;
    protected TicketType $ticketType;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantService = new ParticipantService();
        $this->user = User::factory()->create();
        $this->workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $this->ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
    }

    public function test_create_participant_successfully()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'company' => 'Test Company',
            'position' => 'Developer',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ];

        $participant = $this->participantService->createParticipant($data);

        $this->assertInstanceOf(Participant::class, $participant);
        $this->assertEquals($data['name'], $participant->name);
        $this->assertEquals($data['email'], $participant->email);
        $this->assertEquals($data['phone'], $participant->phone);
        $this->assertEquals($data['company'], $participant->company);
        $this->assertEquals($data['position'], $participant->position);
        $this->assertNotNull($participant->ticket_code);
        $this->assertDatabaseHas('participants', ['email' => $data['email']]);
    }

    public function test_create_participant_generates_unique_ticket_code()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ];

        $participant1 = $this->participantService->createParticipant($data);
        
        $data['email'] = 'jane@example.com';
        $participant2 = $this->participantService->createParticipant($data);

        $this->assertNotEquals($participant1->ticket_code, $participant2->ticket_code);
        $this->assertNotNull($participant1->ticket_code);
        $this->assertNotNull($participant2->ticket_code);
    }

    public function test_update_participant_successfully()
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
        ];

        $updatedParticipant = $this->participantService->updateParticipant($participant, $updateData);

        $this->assertEquals($updateData['name'], $updatedParticipant->name);
        $this->assertEquals($updateData['email'], $updatedParticipant->email);
        $this->assertEquals($updateData['phone'], $updatedParticipant->phone);
        $this->assertEquals($updateData['company'], $updatedParticipant->company);
        $this->assertDatabaseHas('participants', $updateData);
    }

    public function test_delete_participant_successfully()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);

        $result = $this->participantService->deleteParticipant($participant);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('participants', ['id' => $participant->id]);
    }

    public function test_generate_ticket_code_format()
    {
        $ticketCode = $this->participantService->generateTicketCode();

        $this->assertIsString($ticketCode);
        $this->assertEquals(8, strlen($ticketCode));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $ticketCode);
    }

    public function test_generate_ticket_code_uniqueness()
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = $this->participantService->generateTicketCode();
        }

        $uniqueCodes = array_unique($codes);
        $this->assertCount(10, $uniqueCodes);
    }

    public function test_check_in_participant_successfully()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_checked_in' => false,
        ]);

        $checkedInParticipant = $this->participantService->checkIn($participant->ticket_code);

        $this->assertTrue($checkedInParticipant->is_checked_in);
        $this->assertNotNull($checkedInParticipant->checked_in_at);
        $this->assertDatabaseHas('participants', [
            'id' => $participant->id,
            'is_checked_in' => true,
        ]);
    }

    public function test_check_in_with_invalid_ticket_code()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid ticket code.');

        $this->participantService->checkIn('INVALID-CODE');
    }

    public function test_check_in_already_checked_in_participant()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_checked_in' => true,
            'checked_in_at' => now(),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Participant is already checked in at/');

        $this->participantService->checkIn($participant->ticket_code);
    }

    public function test_get_participants_with_filters()
    {
        $participant1 = Participant::factory()->create([
            'name' => 'John Doe',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => true,
        ]);
        $participant2 = Participant::factory()->create([
            'name' => 'Jane Smith',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => false,
        ]);

        // Test without filters
        $allParticipants = $this->participantService->getParticipants();
        $this->assertCount(2, $allParticipants);

        // Test with payment status filter
        $paidParticipants = $this->participantService->getParticipants(['is_paid' => true]);
        $this->assertCount(1, $paidParticipants);
        $this->assertEquals($participant1->id, $paidParticipants->first()->id);

        // Test with search filter
        $searchedParticipants = $this->participantService->getParticipants(['search' => 'John']);
        $this->assertCount(1, $searchedParticipants);
        $this->assertEquals($participant1->id, $searchedParticipants->first()->id);
    }

    public function test_update_payment_status()
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

        $updatedCount = $this->participantService->updatePaymentStatus(
            [$participant1->id, $participant2->id],
            true
        );

        $this->assertEquals(2, $updatedCount);
        $this->assertDatabaseHas('participants', [
            'id' => $participant1->id,
            'is_paid' => true,
        ]);
        $this->assertDatabaseHas('participants', [
            'id' => $participant2->id,
            'is_paid' => true,
        ]);
    }

    public function test_get_workshop_participant_stats()
    {
        // Create participants with different statuses
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => true,
            'is_checked_in' => true,
        ]);
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => false,
            'is_checked_in' => false,
        ]);
        Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => true,
            'is_checked_in' => false,
        ]);

        $stats = $this->participantService->getWorkshopParticipantStats($this->workshop);

        $this->assertIsArray($stats);
        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(2, $stats['paid']);
        $this->assertEquals(1, $stats['unpaid']);
        $this->assertEquals(1, $stats['checked_in']);
        $this->assertEquals(2, $stats['not_checked_in']);
        $this->assertArrayHasKey('by_ticket_type', $stats);
    }

    public function test_create_participant_with_duplicate_email_fails()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ];

        // Create first participant
        $this->participantService->createParticipant($data);

        // Try to create second participant with same email
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A participant with this email already exists in this workshop.');

        $this->participantService->createParticipant($data);
    }

    public function test_create_participant_with_invalid_ticket_type_fails()
    {
        $otherWorkshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $otherTicketType = TicketType::factory()->create(['workshop_id' => $otherWorkshop->id]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $otherTicketType->id, // Wrong workshop
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid ticket type for this workshop.');

        $this->participantService->createParticipant($data);
    }

    public function test_update_participant_with_duplicate_email_fails()
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

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A participant with this email already exists in this workshop.');

        $this->participantService->updateParticipant($participant2, ['email' => 'john@example.com']);
    }

    public function test_import_from_excel_validates_required_fields()
    {
        // Test the Excel header mapping functionality
        $headers = ['Name', 'Email', 'Phone', 'Company'];
        $reflection = new \ReflectionClass($this->participantService);
        $method = $reflection->getMethod('mapExcelHeaders');
        $method->setAccessible(true);
        
        $headerMap = $method->invoke($this->participantService, $headers);
        
        $this->assertArrayHasKey('name', $headerMap);
        $this->assertArrayHasKey('email', $headerMap);
        $this->assertArrayHasKey('phone', $headerMap);
        $this->assertArrayHasKey('company', $headerMap);
        $this->assertEquals(0, $headerMap['name']);
        $this->assertEquals(1, $headerMap['email']);
        $this->assertEquals(2, $headerMap['phone']);
        $this->assertEquals(3, $headerMap['company']);
    }

    public function test_map_row_data_functionality()
    {
        $row = ['John Doe', 'john@example.com', '+1234567890', 'Test Company'];
        $headerMap = ['name' => 0, 'email' => 1, 'phone' => 2, 'company' => 3];
        
        $reflection = new \ReflectionClass($this->participantService);
        $method = $reflection->getMethod('mapRowData');
        $method->setAccessible(true);
        
        $mappedData = $method->invoke($this->participantService, $row, $headerMap);
        
        $this->assertEquals('John Doe', $mappedData['name']);
        $this->assertEquals('john@example.com', $mappedData['email']);
        $this->assertEquals('+1234567890', $mappedData['phone']);
        $this->assertEquals('Test Company', $mappedData['company']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
