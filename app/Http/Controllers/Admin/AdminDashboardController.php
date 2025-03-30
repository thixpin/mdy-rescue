<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get total donations count
        $totalDonations = Donation::count();

        // Get verified donations count
        $verifiedDonations = Donation::where('verified', true)->count();

        // Get pending verifications count
        $pendingVerifications = Donation::where('verified', false)->count();

        // Calculate verification rate
        $verificationRate = $totalDonations > 0
            ? round(($verifiedDonations / $totalDonations) * 100, 1)
            : 0;

        // Get total verified amount
        $totalVerifiedAmount = Donation::where('verified', true)->sum('donation_amount');

        // Get average donation amount
        $averageDonationAmount = Donation::where('verified', true)->avg('donation_amount');

        // Get recent donations
        $recentDonations = Donation::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalDonations',
            'verifiedDonations',
            'pendingVerifications',
            'verificationRate',
            'totalVerifiedAmount',
            'averageDonationAmount',
            'recentDonations'
        ));
    }
}
