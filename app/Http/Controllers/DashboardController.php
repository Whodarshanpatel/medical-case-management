<?php

namespace App\Http\Controllers;

use App\Models\MedicalCase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display medical dashboard with live statistics computed from database.
     */
    public function __invoke(Request $request): View
    {
        $totalCases = MedicalCase::count();
        $planningCases = MedicalCase::where('status', MedicalCase::STATUS_PLANNING)->count();
        $inReviewCases = MedicalCase::where('status', MedicalCase::STATUS_IN_REVIEW)->count();
        $approvedCases = MedicalCase::where('status', MedicalCase::STATUS_APPROVED)->count();
        $completedCases = MedicalCase::where('status', MedicalCase::STATUS_COMPLETED)->count();

        // Additional counts for situational awareness
        $draftCases = MedicalCase::where('status', MedicalCase::STATUS_DRAFT)->count();
        $cancelledCases = MedicalCase::where('status', MedicalCase::STATUS_CANCELLED)->count();

        // Recent cases for fast access
        $recentCases = MedicalCase::with('creator')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalCases',
            'planningCases',
            'inReviewCases',
            'approvedCases',
            'completedCases',
            'draftCases',
            'cancelledCases',
            'recentCases'
        ));
    }
}
