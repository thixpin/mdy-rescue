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

        // Get total verified amounts by currency
        $totalVerifiedAmounts = Donation::where('verified', true)
            ->selectRaw('currency, SUM(donation_amount) as total')
            ->groupBy('currency')
            ->get()
            ->map(function ($item) {
                return [
                    'currency' => $item->currency,
                    'amount' => $item->total,
                    'formatted' => $item->currency->format($item->total),
                    'label' => $item->currency->label(),
                ];
            });

        // Get average donation amounts by currency
        $averageDonationAmounts = Donation::where('verified', true)
            ->selectRaw('currency, AVG(donation_amount) as average')
            ->groupBy('currency')
            ->get()
            ->map(function ($item) {
                return [
                    'currency' => $item->currency,
                    'amount' => $item->average,
                    'formatted' => $item->currency->format($item->average),
                    'label' => $item->currency->label(),
                ];
            });

        // Get recent donations
        $recentDonations = Donation::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalDonations',
            'verifiedDonations',
            'pendingVerifications',
            'verificationRate',
            'totalVerifiedAmounts',
            'averageDonationAmounts',
            'recentDonations'
        ));
    }
}
