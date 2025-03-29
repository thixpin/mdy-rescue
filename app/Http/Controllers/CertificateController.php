<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function verify(Request $request)
    {
        $shortId = strtoupper($request->input('short_id'));
        $donation = Donation::where('short_id', $shortId)->first();

        if (! $donation) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate not found',
            ], 404);
        }

        try {
            $isValid = $this->certificateService->verifyCertificate($donation);

            return response()->json([
                'success' => true,
                'is_valid' => $isValid,
                'message' => $isValid ? 'Certificate is valid' : 'Certificate is invalid or not accessible',
                'donation' => [
                    'name' => $donation->name,
                    'description' => $donation->description,
                    'donation_amount' => $donation->donation_amount,
                    'donate_date' => $donation->donate_date->format('Y-m-d'),
                    'certificate_url' => $donation->certificate_url,
                    'short_id' => $donation->short_id,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify certificate: '.$e->getMessage(),
            ], 500);
        }
    }

    public function showVerificationPage()
    {
        $shortId = request()->get('id');

        return view('certificates.verify', compact('shortId'));
    }
}
