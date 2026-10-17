<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DormController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'room_type' => ['nullable', 'in:single,shared,bedspace'],
        ]);

        $properties = Property::query()
            ->whereHas('rooms', function ($q) use ($filters) {
                $q->where('status', 'available');
                if (! empty($filters['max_price'])) {
                    $q->where('price', '<=', $filters['max_price']);
                }
                if (! empty($filters['room_type'])) {
                    $q->where('room_type', $filters['room_type']);
                }
            })
            ->when($filters['q'] ?? null, function ($query, $search) {
                $query->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->with('images')
            ->withMin(['rooms as min_price' => fn ($q) => $q->where('status', 'available')], 'price')
            ->latest()
            ->get()
            ->map(fn (Property $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'address' => $p->address,
                'is_verified' => $p->is_verified,
                'min_price' => $p->min_price,
                'cover' => $p->images->first() ? Storage::url($p->images->first()->path) : null,
            ]);

        return Inertia::render('student/dorms/Index', [
            'properties' => $properties,
            'filters' => [
                'q' => $filters['q'] ?? '',
                'max_price' => $filters['max_price'] ?? '',
                'room_type' => $filters['room_type'] ?? '',
            ],
        ]);
    }

    public function show(Property $property): Response
    {
        $property->load(['images', 'landlord', 'rooms' => fn ($q) => $q->orderBy('price')]);

        return Inertia::render('student/dorms/Show', [
            'property' => [
                'id' => $property->id,
                'name' => $property->name,
                'address' => $property->address,
                'description' => $property->description,
                'rules' => $property->rules,
                'is_verified' => $property->is_verified,
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'landlord' => [
                    'name' => $property->landlord->name,
                    'phone' => $property->landlord->phone,
                ],
                'images' => $property->images->map(fn ($i) => [
                    'id' => $i->id,
                    'url' => Storage::url($i->path),
                ]),
                'rooms' => $property->rooms->map(fn ($r) => [
                    'id' => $r->id,
                    'room_type' => $r->room_type,
                    'capacity' => $r->capacity,
                    'available_slots' => $r->available_slots,
                    'price' => $r->price,
                    'status' => $r->status,
                ]),
            ],
        ]);
    }
}
