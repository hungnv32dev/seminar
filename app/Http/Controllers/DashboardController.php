<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\Participant;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Get workshop statistics
        $totalWorkshops = Workshop::count();
        $activeWorkshops = Workshop::where('status', 'published')->orWhere('status', 'ongoing')->count();
        $upcomingWorkshops = Workshop::upcoming()->count();
        $completedWorkshops = Workshop::where('status', 'completed')->count();

        // Get participant statistics
        $totalParticipants = Participant::count();
        $checkedInParticipants = Participant::where('is_checked_in', true)->count();
        $paidParticipants = Participant::where('is_paid', true)->count();

        // Get revenue statistics
        $totalRevenue = Participant::join('ticket_types', 'participants.ticket_type_id', '=', 'ticket_types.id')
            ->where('participants.is_paid', true)
            ->sum('ticket_types.price');

        // Get recent workshops
        $recentWorkshops = Workshop::with(['creator', 'participants'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get workshop status distribution
        $workshopsByStatus = Workshop::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Get monthly workshop creation trend (last 6 months)
        $monthlyWorkshops = Workshop::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $data = [
            'totalWorkshops' => $totalWorkshops,
            'activeWorkshops' => $activeWorkshops,
            'upcomingWorkshops' => $upcomingWorkshops,
            'completedWorkshops' => $completedWorkshops,
            'totalParticipants' => $totalParticipants,
            'checkedInParticipants' => $checkedInParticipants,
            'paidParticipants' => $paidParticipants,
            'totalRevenue' => $totalRevenue,
            'recentWorkshops' => $recentWorkshops,
            'workshopsByStatus' => $workshopsByStatus,
            'monthlyWorkshops' => $monthlyWorkshops,
            'checkInRate' => $totalParticipants > 0 ? round(($checkedInParticipants / $totalParticipants) * 100, 1) : 0,
            'paymentRate' => $totalParticipants > 0 ? round(($paidParticipants / $totalParticipants) * 100, 1) : 0,
        ];
        
        return view('dashboard', compact('data'));
    }
}
