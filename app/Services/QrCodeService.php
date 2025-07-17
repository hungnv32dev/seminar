<?php

namespace App\Services;

use App\Models\Participant;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Exception;

class QrCodeService
{
    /**
     * Generate QR code for a ticket code.
     */
    public function generateQrCode(string $ticketCode, int $size = 200): string
    {
        try {
            return QrCode::size($size)
                ->format('png')
                ->generate($ticketCode);
        } catch (Exception $e) {
            throw new Exception('Failed to generate QR code: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR code and save to storage.
     */
    public function generateAndSaveQrCode(string $ticketCode, int $size = 200): string
    {
        try {
            $qrCode = $this->generateQrCode($ticketCode, $size);
            
            // Create filename
            $filename = 'qr-codes/' . $ticketCode . '.png';
            
            // Save to storage
            Storage::disk('public')->put($filename, $qrCode);
            
            return $filename;
        } catch (Exception $e) {
            throw new Exception('Failed to generate and save QR code: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR code URL for email templates.
     */
    public function generateQrCodeUrl(string $ticketCode): string
    {
        return route('qr-code.generate', ['ticket_code' => $ticketCode]);
    }

    /**
     * Generate QR code data URL (base64 encoded).
     */
    public function generateQrCodeDataUrl(string $ticketCode, int $size = 200): string
    {
        try {
            $qrCode = $this->generateQrCode($ticketCode, $size);
            return 'data:image/png;base64,' . base64_encode($qrCode);
        } catch (Exception $e) {
            throw new Exception('Failed to generate QR code data URL: ' . $e->getMessage());
        }
    }

    /**
     * Decode QR code data (for scanning).
     */
    public function decodeQrCode(string $qrData): string
    {
        // For simple text QR codes, the data is the ticket code itself
        // In more complex scenarios, you might need additional decoding logic
        return trim($qrData);
    }

    /**
     * Validate ticket code from QR scan.
     */
    public function validateTicketCode(string $ticketCode): array
    {
        $participant = Participant::where('ticket_code', $ticketCode)
            ->with(['workshop', 'ticketType'])
            ->first();

        if (!$participant) {
            return [
                'valid' => false,
                'message' => 'Invalid ticket code.',
                'participant' => null,
            ];
        }

        // Check if workshop is active
        $workshop = $participant->workshop;
        $now = now();

        if ($workshop->status === 'cancelled') {
            return [
                'valid' => false,
                'message' => 'This workshop has been cancelled.',
                'participant' => $participant,
            ];
        }

        if ($workshop->status === 'completed') {
            return [
                'valid' => false,
                'message' => 'This workshop has already ended.',
                'participant' => $participant,
            ];
        }

        // Check if participant is already checked in
        if ($participant->is_checked_in) {
            $checkedInTime = $participant->checked_in_at ? $participant->checked_in_at->format('Y-m-d H:i:s') : 'unknown time';
            return [
                'valid' => true,
                'message' => 'Participant is already checked in at ' . $checkedInTime,
                'participant' => $participant,
                'already_checked_in' => true,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Valid ticket code.',
            'participant' => $participant,
            'already_checked_in' => false,
        ];
    }

    /**
     * Generate QR code for check-in with additional data.
     */
    public function generateCheckInQrCode(Participant $participant, int $size = 200): string
    {
        $data = [
            'ticket_code' => $participant->ticket_code,
            'workshop_id' => $participant->workshop_id,
            'participant_id' => $participant->id,
            'timestamp' => now()->timestamp,
        ];

        $qrData = json_encode($data);

        try {
            return QrCode::size($size)
                ->format('png')
                ->generate($qrData);
        } catch (Exception $e) {
            throw new Exception('Failed to generate check-in QR code: ' . $e->getMessage());
        }
    }

    /**
     * Decode check-in QR code data.
     */
    public function decodeCheckInQrCode(string $qrData): array
    {
        try {
            $data = json_decode($qrData, true);

            if (!$data || !isset($data['ticket_code'])) {
                // Fallback to simple ticket code
                return ['ticket_code' => trim($qrData)];
            }

            return $data;
        } catch (Exception $e) {
            // Fallback to simple ticket code
            return ['ticket_code' => trim($qrData)];
        }
    }

    /**
     * Generate batch QR codes for multiple participants.
     */
    public function generateBatchQrCodes(array $participants, int $size = 200): array
    {
        $results = [];
        $errors = [];

        foreach ($participants as $participant) {
            try {
                if ($participant instanceof Participant) {
                    $ticketCode = $participant->ticket_code;
                } else {
                    $ticketCode = $participant['ticket_code'] ?? null;
                }

                if (!$ticketCode) {
                    $errors[] = 'Missing ticket code for participant';
                    continue;
                }

                $qrCode = $this->generateQrCode($ticketCode, $size);
                $results[$ticketCode] = $qrCode;

            } catch (Exception $e) {
                $errors[] = 'Failed to generate QR code for ' . ($ticketCode ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        return [
            'qr_codes' => $results,
            'errors' => $errors,
            'total_generated' => count($results),
            'total_errors' => count($errors),
        ];
    }

    /**
     * Generate QR code for workshop information.
     */
    public function generateWorkshopQrCode($workshop, int $size = 200): string
    {
        $data = [
            'type' => 'workshop',
            'workshop_id' => $workshop->id,
            'name' => $workshop->name,
            'start_date' => $workshop->start_date->toISOString(),
            'location' => $workshop->location,
            'url' => route('workshops.show', $workshop->id),
        ];

        $qrData = json_encode($data);

        try {
            return QrCode::size($size)
                ->format('png')
                ->generate($qrData);
        } catch (Exception $e) {
            throw new Exception('Failed to generate workshop QR code: ' . $e->getMessage());
        }
    }

    /**
     * Clean up old QR code files.
     */
    public function cleanupOldQrCodes(int $daysOld = 30): int
    {
        $files = Storage::disk('public')->files('qr-codes');
        $deletedCount = 0;
        $cutoffDate = now()->subDays($daysOld);

        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            
            if ($lastModified < $cutoffDate->timestamp) {
                Storage::disk('public')->delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Get QR code file path for a ticket code.
     */
    public function getQrCodePath(string $ticketCode): string
    {
        return 'qr-codes/' . $ticketCode . '.png';
    }

    /**
     * Check if QR code file exists.
     */
    public function qrCodeExists(string $ticketCode): bool
    {
        return Storage::disk('public')->exists($this->getQrCodePath($ticketCode));
    }

    /**
     * Get QR code public URL.
     */
    public function getQrCodePublicUrl(string $ticketCode): string
    {
        $path = $this->getQrCodePath($ticketCode);
        
        if (!$this->qrCodeExists($ticketCode)) {
            // Generate QR code if it doesn't exist
            $this->generateAndSaveQrCode($ticketCode);
        }

        return Storage::disk('public')->url($path);
    }
}