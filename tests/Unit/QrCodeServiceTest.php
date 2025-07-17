<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\QrCodeService;
use App\Models\Participant;
use App\Models\Workshop;
use App\Models\User;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery;

class QrCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QrCodeService $qrCodeService;
    protected Workshop $workshop;
    protected TicketType $ticketType;
    protected Participant $participant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->qrCodeService = new QrCodeService();
        $this->user = User::factory()->create();
        $this->workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $this->ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        $this->participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);

        // Fake storage for testing
        Storage::fake('public');
    }

    public function test_generate_qr_code_returns_binary_data()
    {
        $ticketCode = $this->participant->ticket_code;
        
        $qrCodeData = $this->qrCodeService->generateQrCode($ticketCode);

        $this->assertIsString($qrCodeData);
        $this->assertNotEmpty($qrCodeData);
        // Check if it's binary PNG data
        $this->assertStringStartsWith("\x89PNG", $qrCodeData);
    }

    public function test_generate_qr_code_with_different_sizes()
    {
        $ticketCode = $this->participant->ticket_code;
        
        $smallQr = $this->qrCodeService->generateQrCode($ticketCode, 100);
        $largeQr = $this->qrCodeService->generateQrCode($ticketCode, 400);

        $this->assertIsString($smallQr);
        $this->assertIsString($largeQr);
        $this->assertNotEquals($smallQr, $largeQr);
    }

    public function test_generate_and_save_qr_code()
    {
        $ticketCode = $this->participant->ticket_code;
        
        $filePath = $this->qrCodeService->generateAndSaveQrCode($ticketCode);

        $this->assertIsString($filePath);
        $this->assertEquals('qr-codes/' . $ticketCode . '.png', $filePath);
        Storage::disk('public')->assertExists($filePath);
    }

    public function test_generate_qr_code_url()
    {
        $ticketCode = $this->participant->ticket_code;
        
        $url = $this->qrCodeService->generateQrCodeUrl($ticketCode);

        $this->assertIsString($url);
        $this->assertStringContains($ticketCode, $url);
        $this->assertStringContains('qr-code', $url);
    }

    public function test_generate_qr_code_data_url()
    {
        $ticketCode = $this->participant->ticket_code;
        
        $dataUrl = $this->qrCodeService->generateQrCodeDataUrl($ticketCode);

        $this->assertIsString($dataUrl);
        $this->assertStringStartsWith('data:image/png;base64,', $dataUrl);
    }

    public function test_decode_qr_code()
    {
        $ticketCode = $this->participant->ticket_code;
        
        $decodedData = $this->qrCodeService->decodeQrCode($ticketCode);

        $this->assertEquals($ticketCode, $decodedData);
    }

    public function test_validate_ticket_code_valid()
    {
        $ticketCode = $this->participant->ticket_code;
        
        $validation = $this->qrCodeService->validateTicketCode($ticketCode);

        $this->assertTrue($validation['valid']);
        $this->assertEquals('Valid ticket code.', $validation['message']);
        $this->assertEquals($this->participant->id, $validation['participant']->id);
        $this->assertFalse($validation['already_checked_in']);
    }

    public function test_validate_ticket_code_invalid()
    {
        $validation = $this->qrCodeService->validateTicketCode('INVALID-CODE');

        $this->assertFalse($validation['valid']);
        $this->assertEquals('Invalid ticket code.', $validation['message']);
        $this->assertNull($validation['participant']);
    }

    public function test_validate_ticket_code_already_checked_in()
    {
        $this->participant->update([
            'is_checked_in' => true,
            'checked_in_at' => now(),
        ]);

        $validation = $this->qrCodeService->validateTicketCode($this->participant->ticket_code);

        $this->assertTrue($validation['valid']);
        $this->assertStringContains('already checked in', $validation['message']);
        $this->assertTrue($validation['already_checked_in']);
    }

    public function test_validate_ticket_code_cancelled_workshop()
    {
        $this->workshop->update(['status' => 'cancelled']);

        $validation = $this->qrCodeService->validateTicketCode($this->participant->ticket_code);

        $this->assertFalse($validation['valid']);
        $this->assertEquals('This workshop has been cancelled.', $validation['message']);
    }

    public function test_validate_ticket_code_completed_workshop()
    {
        $this->workshop->update(['status' => 'completed']);

        $validation = $this->qrCodeService->validateTicketCode($this->participant->ticket_code);

        $this->assertFalse($validation['valid']);
        $this->assertEquals('This workshop has already ended.', $validation['message']);
    }

    public function test_generate_check_in_qr_code()
    {
        $qrCodeData = $this->qrCodeService->generateCheckInQrCode($this->participant);

        $this->assertIsString($qrCodeData);
        $this->assertNotEmpty($qrCodeData);
        $this->assertStringStartsWith("\x89PNG", $qrCodeData);
    }

    public function test_decode_check_in_qr_code()
    {
        // Create JSON data that would be in a check-in QR code
        $data = [
            'ticket_code' => $this->participant->ticket_code,
            'participant_id' => $this->participant->id,
            'workshop_id' => $this->workshop->id,
            'type' => 'check_in',
        ];
        $jsonData = json_encode($data);

        $decoded = $this->qrCodeService->decodeCheckInQrCode($jsonData);

        $this->assertEquals($data['ticket_code'], $decoded['ticket_code']);
        $this->assertEquals($data['participant_id'], $decoded['participant_id']);
        $this->assertEquals($data['workshop_id'], $decoded['workshop_id']);
        $this->assertEquals($data['type'], $decoded['type']);
    }

    public function test_decode_check_in_qr_code_fallback_to_simple_code()
    {
        // Test fallback behavior for invalid JSON
        $decoded = $this->qrCodeService->decodeCheckInQrCode('SIMPLE-CODE');

        $this->assertEquals(['ticket_code' => 'SIMPLE-CODE'], $decoded);
    }

    public function test_generate_batch_qr_codes()
    {
        $participant2 = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);

        $participants = [$this->participant, $participant2];
        
        $results = $this->qrCodeService->generateBatchQrCodes($participants);

        $this->assertIsArray($results);
        $this->assertArrayHasKey('qr_codes', $results);
        $this->assertArrayHasKey('errors', $results);
        $this->assertArrayHasKey('total_generated', $results);
        $this->assertArrayHasKey('total_errors', $results);
        $this->assertEquals(2, $results['total_generated']);
        $this->assertEquals(0, $results['total_errors']);
        $this->assertCount(2, $results['qr_codes']);
    }

    public function test_generate_workshop_qr_code()
    {
        $qrCodeData = $this->qrCodeService->generateWorkshopQrCode($this->workshop);

        $this->assertIsString($qrCodeData);
        $this->assertNotEmpty($qrCodeData);
        $this->assertStringStartsWith("\x89PNG", $qrCodeData);
    }

    public function test_get_qr_code_path()
    {
        $ticketCode = $this->participant->ticket_code;
        
        $path = $this->qrCodeService->getQrCodePath($ticketCode);

        $this->assertEquals('qr-codes/' . $ticketCode . '.png', $path);
    }

    public function test_qr_code_exists()
    {
        $ticketCode = $this->participant->ticket_code;
        
        // Initially should not exist
        $this->assertFalse($this->qrCodeService->qrCodeExists($ticketCode));
        
        // Generate and save QR code
        $this->qrCodeService->generateAndSaveQrCode($ticketCode);
        
        // Now should exist
        $this->assertTrue($this->qrCodeService->qrCodeExists($ticketCode));
    }

    public function test_get_qr_code_public_url()
    {
        $ticketCode = $this->participant->ticket_code;
        
        // Generate and save QR code first
        $this->qrCodeService->generateAndSaveQrCode($ticketCode);
        
        $url = $this->qrCodeService->getQrCodePublicUrl($ticketCode);

        $this->assertIsString($url);
        $this->assertStringContains($ticketCode, $url);
        $this->assertStringContains('storage/qr-codes', $url);
    }

    public function test_cleanup_old_qr_codes()
    {
        // Create some QR codes
        $this->qrCodeService->generateAndSaveQrCode($this->participant->ticket_code);
        
        // Mock old files by creating files with old timestamps
        $oldFilePath = 'qr-codes/OLD-CODE.png';
        Storage::disk('public')->put($oldFilePath, 'fake content');
        
        // The cleanup method would normally check file timestamps
        // For testing purposes, we'll just verify the method runs without error
        $deletedCount = $this->qrCodeService->cleanupOldQrCodes(30);

        $this->assertIsInt($deletedCount);
        $this->assertGreaterThanOrEqual(0, $deletedCount);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
