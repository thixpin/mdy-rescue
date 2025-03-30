<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\DonationsImport;
use App\Models\Donation;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DonationController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index()
    {
        $donations = Donation::latest()->paginate(10);

        return view('admin.donations.index', compact('donations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'donation_amount' => 'required|numeric|min:0',
            'amount_in_text' => 'required|string',
            'donate_date' => 'required|date',
            'verified' => 'boolean',
        ]);

        Donation::create($validated);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation added successfully.');
    }

    public function edit(Donation $donation)
    {
        if (request()->ajax()) {
            return response()->json($donation);
        }

        return view('admin.donations.edit', compact('donation'));
    }

    public function update(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'donation_amount' => 'required|numeric|min:0',
            'amount_in_text' => 'required|string',
            'donate_date' => 'required|date',
            'verified' => 'boolean',
        ]);

        $donation->update($validated);

        if ($donation->verified) {
            $this->certificateService->generateAndUploadCertificate($donation);
        }

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation updated successfully.');
    }

    public function destroy(Donation $donation)
    {
        $donation->delete();

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation deleted successfully.');
    }

    public function toggleVerification(Donation $donation)
    {
        $newVerifiedStatus = ! $donation->verified;

        // Generate certificate when verifying
        if ($newVerifiedStatus) {
            try {
                $this->certificateService->generateAndUploadCertificate($donation);

                return redirect()->route('admin.donations.index')
                    ->with('success', 'Donation verified and certificate generated successfully.');

            } catch (\Exception $e) {
                Log::error('Failed to generate certificate for donation '.$donation->short_id.': '.$e->getMessage());

                return redirect()->route('admin.donations.index')
                    ->with('error', 'Failed to generate certificate for donation '.$donation->short_id);
            }
        } else {
            $donation->update([
                'verified' => $newVerifiedStatus,
                'certificate_url' => null,
            ]);
        }

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation verification status updated successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new DonationsImport, $request->file('file'));

            return redirect()->route('admin.donations.index')
                ->with('success', 'Donations imported successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.donations.index')
                ->with('error', 'Error importing donations: '.$e->getMessage());
        }
    }

    public function generateCertificate(Donation $donation)
    {
        try {
            $certificateUrl = $this->certificateService->generateAndUploadCertificate($donation);

            return response()->json([
                'success' => true,
                'message' => 'Certificate generated successfully',
                'certificate_url' => $certificateUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate certificate: '.$e->getMessage(),
            ], 500);
        }
    }

    public function verifyCertificate(Donation $donation)
    {
        try {
            $isValid = $this->certificateService->verifyCertificate($donation);

            return response()->json([
                'success' => true,
                'is_valid' => $isValid,
                'message' => $isValid ? 'Certificate is valid' : 'Certificate is invalid or not accessible',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify certificate: '.$e->getMessage(),
            ], 500);
        }
    }
}
