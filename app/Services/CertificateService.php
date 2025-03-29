<?php

namespace App\Services;

use App\Models\Donation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function generateAndUploadCertificate(Donation $donation)
    {
        try {
            // Generate PDF certificate
            $pdf = PDF::loadView('certificates.donation', [
                'donation' => $donation,
            ]);

            // Generate unique filename
            $filename = "certificates/{$donation->short_id}.pdf";

            // Upload to S3
            Storage::disk('s3')->put($filename, $pdf->output());

            // Get the public URL
            $certificateUrl = Storage::disk('s3')->url($filename);

            // Update donation with new certificate URL
            $donation->update([
                'certificate_url' => $certificateUrl,
            ]);

            return $certificateUrl;
        } catch (\Exception $e) {
            Log::error('Certificate generation failed: '.$e->getMessage());
            throw $e;
        }
    }

    public function verifyCertificate(Donation $donation)
    {
        if (! $donation->certificate_url) {
            return false;
        }

        try {
            $filename = "certificates/{$donation->short_id}.pdf";

            return Storage::disk('s3')->exists($filename);
        } catch (\Exception $e) {
            Log::error('Certificate verification failed for donation '.$donation->short_id.': '.$e->getMessage());

            return false;
        }
    }
}
