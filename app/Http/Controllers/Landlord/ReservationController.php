<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Notifications\ReservationStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function index(Request $request): Response
    {
        $reservations = Reservation::query()
            ->whereHas('room.property', fn ($q) => $q->where('landlord_id', $request->user()->id))
            ->with(['student', 'room.property'])
            ->latest()
            ->get()
            ->map(fn (Reservation $r) => [
                'id' => $r->id,
                'student' => $r->student->name,
                'student_email' => $r->student->email,
                'student_phone' => $r->student->phone,
                'property' => $r->room->property->name,
                'room_type' => $r->room->room_type,
                'price' => $r->room->price,
                'move_in_date' => $r->move_in_date->format('M d, Y'),
                'status' => $r->status,
                'message' => $r->message,
            ]);

        return Inertia::render('landlord/reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function approve(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorizeOwner($request, $reservation);
        abort_unless($reservation->status === 'pending', 422);

        $room = $reservation->room;
        abort_unless($room->available_slots > 0, 422, 'No slots left.');

        $room->available_slots -= 1;
        if ($room->available_slots === 0) {
            $room->status = 'full';
        }
        $room->save();

        $reservation->update(['status' => 'approved']);
        $this->notifyStudent($reservation);

        return back();
    }

    public function decline(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorizeOwner($request, $reservation);
        abort_unless($reservation->status === 'pending', 422);

        $reservation->update(['status' => 'declined']);
        $this->notifyStudent($reservation);

        return back();
    }

    private function notifyStudent(Reservation $reservation): void
    {
        try {
            $reservation->student->notify(new ReservationStatusUpdated($reservation));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function authorizeOwner(Request $request, Reservation $reservation): void
    {
        abort_unless($reservation->room->property->landlord_id === $request->user()->id, 403);
    }
}
