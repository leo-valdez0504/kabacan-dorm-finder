<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        if ($user->isLandlord()) {
            $data = $this->landlord($user);
        } elseif ($user->isAdmin()) {
            $data = $this->admin();
        } else {
            $data = $this->student($user);
        }

        return Inertia::render('Dashboard', [
            'role' => $user->role,
            'name' => $user->name,
            ...$data,
        ]);
    }

    private function landlord(User $user): array
    {
        $mine = fn ($q) => $q->where('landlord_id', $user->id);

        $reservations = Reservation::whereHas('room.property', $mine);
        $rooms = Room::whereHas('property', $mine);

        return [
            'stats' => [
                ['label' => 'My properties', 'value' => $user->properties()->count()],
                ['label' => 'Rooms', 'value' => (clone $rooms)->count()],
                ['label' => 'Open slots', 'value' => (int) (clone $rooms)->sum('available_slots')],
                ['label' => 'Pending requests', 'value' => (clone $reservations)->where('status', 'pending')->count()],
            ],
            'links' => [
                ['label' => 'Add property', 'href' => '/landlord/properties/create'],
                ['label' => 'Review requests', 'href' => '/landlord/reservations'],
                ['label' => 'View reports', 'href' => '/landlord/reports'],
            ],
            'recentTitle' => 'Latest reservation requests',
            'recent' => (clone $reservations)
                ->with(['student', 'room.property'])
                ->latest()->take(5)->get()
                ->map(fn (Reservation $r) => [
                    'id' => $r->id,
                    'title' => $r->student->name.' · '.$r->room->property->name,
                    'subtitle' => ucfirst($r->room->room_type).' · Move-in '.$r->move_in_date->format('M d, Y'),
                    'badge' => $r->status,
                    'href' => '/landlord/reservations',
                ]),
        ];
    }

    private function student(User $user): array
    {
        $mine = $user->reservations();

        return [
            'stats' => [
                ['label' => 'Total requests', 'value' => (clone $mine)->count()],
                ['label' => 'Pending', 'value' => (clone $mine)->where('status', 'pending')->count()],
                ['label' => 'Approved', 'value' => (clone $mine)->where('status', 'approved')->count()],
                ['label' => 'Available dorms', 'value' => Property::whereHas('rooms', fn ($q) => $q->where('status', 'available'))->count()],
            ],
            'links' => [
                ['label' => 'Find a dorm', 'href' => '/student/dorms'],
                ['label' => 'My reservations', 'href' => '/student/reservations'],
            ],
            'recentTitle' => 'My latest requests',
            'recent' => $user->reservations()
                ->with('room.property')
                ->latest()->take(5)->get()
                ->map(fn (Reservation $r) => [
                    'id' => $r->id,
                    'title' => $r->room->property->name,
                    'subtitle' => ucfirst($r->room->room_type).' · ₱'.$r->room->price.' · Move-in '.$r->move_in_date->format('M d, Y'),
                    'badge' => $r->status,
                    'href' => '/student/reservations',
                ]),
        ];
    }

    private function admin(): array
    {
        return [
            'stats' => [
                ['label' => 'Students', 'value' => User::where('role', 'student')->count()],
                ['label' => 'Landlords', 'value' => User::where('role', 'landlord')->count()],
                ['label' => 'Properties', 'value' => Property::count()],
                ['label' => 'Awaiting verification', 'value' => Property::where('is_verified', false)->count()],
            ],
            'links' => [
                ['label' => 'Verify properties', 'href' => '/admin/properties'],
                ['label' => 'Manage users', 'href' => '/admin/users'],
            ],
            'recentTitle' => 'Properties awaiting verification',
            'recent' => Property::with('landlord')
                ->where('is_verified', false)
                ->latest()->take(5)->get()
                ->map(fn (Property $p) => [
                    'id' => $p->id,
                    'title' => $p->name,
                    'subtitle' => $p->address.' · Landlord: '.$p->landlord->name,
                    'badge' => 'pending',
                    'href' => '/admin/properties',
                ]),
        ];
    }
}