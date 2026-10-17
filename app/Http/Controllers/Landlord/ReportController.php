<?php

namespace App\Http\Controllers\Landlord;

use App\Exports\ReservationsExport;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $id = $request->user()->id;

        $reservations = $this->reservationsFor($id);
        $rooms = Room::whereHas('property', fn ($q) => $q->where('landlord_id', $id))->get();

        $statuses = ['pending', 'approved', 'declined', 'cancelled'];

        return Inertia::render('landlord/reports/Index', [
            'statusLabels' => $statuses,
            'statusCounts' => collect($statuses)
                ->map(fn ($s) => $reservations->where('status', $s)->count())
                ->values(),
            'roomLabels' => ['single', 'shared', 'bedspace'],
            'roomCounts' => collect(['single', 'shared', 'bedspace'])
                ->map(fn ($t) => $rooms->where('room_type', $t)->count())
                ->values(),
            'totals' => [
                'reservations' => $reservations->count(),
                'rooms' => $rooms->count(),
                'open_slots' => $rooms->sum('available_slots'),
            ],
        ]);
    }

    public function pdf(Request $request)
    {
        $reservations = $this->reservationsFor($request->user()->id);

        $pdf = Pdf::loadView('reports.reservations', [
            'landlord' => $request->user()->name,
            'reservations' => $reservations,
            'generatedAt' => now()->format('M d, Y h:i A'),
        ]);

        return $pdf->download('reservations-report.pdf');
    }

    public function excel(Request $request)
    {
        return Excel::download(new ReservationsExport($request->user()->id), 'reservations.xlsx');
    }

    private function reservationsFor(int $landlordId)
    {
        return Reservation::query()
            ->whereHas('room.property', fn ($q) => $q->where('landlord_id', $landlordId))
            ->with(['student', 'room.property'])
            ->latest()
            ->get();
    }
}