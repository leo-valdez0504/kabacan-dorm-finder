<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Notifications\NewReservationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function index(Request $request): Response
    {
        $reservations = $request->user()
            ->reservations()
            ->with('room.property')
            ->latest()
            ->get()
            ->map(fn (Reservation $r) => [
                'id' => $r->id,
                'property' => $r->room->property->name,
                'address' => $r->room->property->address,
                'room_type' => $r->room->room_type,
                'price' => $r->room->price,
                'move_in_date' => $r->move_in_date->format('M d, Y'),
                'status' => $r->status,
                'message' => $r->message,
            ]);

        return Inertia::render('student/reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function store(Request $request, Room $room): RedirectResponse
    {
        $data = $request->validate([
            'move_in_date' => ['required', 'date', 'after_or_equal:today'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        abort_unless($room->status === 'available' && $room->available_slots > 0, 422, 'This room is full.');

        $exists = Reservation::where('student_id', $request->user()->id)
            ->where('room_id', $room->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['move_in_date' => 'You already have an active request for this room.']);
        }

        $reservation = Reservation::create([
            'student_id' => $request->user()->id,
            'room_id' => $room->id,
            'move_in_date' => $data['move_in_date'],
            'message' => $data['message'] ?? null,
            'status' => 'pending',
        ]);

        try {
            $room->loadMissing('property.landlord');
            $room->property->landlord->notify(new NewReservationRequest($reservation));
        } catch (\Throwable $e) {
            report($e);
        }

        return to_route('student.reservations.index');
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        abort_unless($reservation->student_id === $request->user()->id, 403);
        abort_unless(in_array($reservation->status, ['pending', 'approved']), 422);

        if ($reservation->status === 'approved') {
            $room = $reservation->room;
            $room->available_slots = min($room->capacity, $room->available_slots + 1);
            $room->status = 'available';
            $room->save();
        }

        $reservation->update(['status' => 'cancelled']);

        return back();
    }
}
