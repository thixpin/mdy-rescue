<?php

namespace App\Services;

use App\Helpers\Formatter;
use App\Models\Donation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class CertificateService
{
    public function generateAndUploadCertificate(Donation $donation)
    {
        try {
            // Generate PDF certificate
            $donationCopy = $donation->replicate();
            $donationCopy->name = $donationCopy->name;
            $donationCopy->description = $donationCopy->description;
            $donationCopy->amount = ($donationCopy->currency->value == 'MMK') ?
                            'အလှူတော်ငွေ ( '.$donationCopy->formatted_amount.' ) ကျပ်' :
                            $donationCopy->formatted_amount;
            $donationCopy->amount_in_text = ($donationCopy->currency->value == 'MMK' && $donationCopy->amount_in_text != '') ?
                            $donationCopy->amount_in_text.' တိတိ' :
                            $donationCopy->amount_in_text;
            $donationCopy->formated_date = Formatter::convertToMmNumber($donationCopy->donate_date->format('d  m  Y'));

            $pdf = Pdf::view('certificates.donation', [
                'donation' => $donationCopy,
            ])
                ->format('A4')
                ->orientation('portrait')
                ->margins(0, 0, 0, 0);

            // Generate unique filename
            $filename = "certificates/{$donation->short_id}.pdf";

            // Upload to S3
            $pdf->disk('s3')->save($filename);

            // Get the public URL
            $certificateUrl = Storage::disk('s3')->url($filename);

            // Update donation with new certificate URL
            $donation->update([
                'certificate_url' => $certificateUrl,
                'verified' => true,
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
