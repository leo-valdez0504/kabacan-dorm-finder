<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function index(Request $request): Response
    {
        $properties = $request->user()
            ->properties()
            ->withCount('rooms')
            ->with('images')
            ->latest()
            ->get()
            ->map(fn (Property $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'address' => $p->address,
                'is_verified' => $p->is_verified,
                'rooms_count' => $p->rooms_count,
                'cover' => $p->images->first() ? Storage::url($p->images->first()->path) : null,
            ]);

        return Inertia::render('landlord/properties/Index', [
            'properties' => $properties,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('landlord/properties/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'rules' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $property = $request->user()->properties()->create(Arr::except($data, ['images']));

        foreach ($request->file('images', []) as $file) {
            $property->images()->create([
                'path' => $file->store("properties/{$property->id}", 'public'),
            ]);
        }

        return to_route('landlord.properties.show', $property);
    }

    public function show(Request $request, Property $property): Response
    {
        $this->authorizeOwner($request, $property);

        $property->load(['rooms', 'images']);

        return Inertia::render('landlord/properties/Show', [
            'property' => [
                'id' => $property->id,
                'name' => $property->name,
                'address' => $property->address,
                'description' => $property->description,
                'rules' => $property->rules,
                'is_verified' => $property->is_verified,
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

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        $this->authorizeOwner($request, $property);

        Storage::disk('public')->deleteDirectory("properties/{$property->id}");
        $property->delete();

        return to_route('landlord.properties.index');
    }

    private function authorizeOwner(Request $request, Property $property): void
    {
        abort_unless($property->landlord_id === $request->user()->id, 403);
    }
}
