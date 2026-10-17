<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function index(): Response
    {
        $properties = Property::query()
            ->with('landlord')
            ->withCount('rooms')
            ->latest()
            ->get()
            ->map(fn (Property $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'address' => $p->address,
                'landlord' => $p->landlord->name,
                'landlord_email' => $p->landlord->email,
                'rooms_count' => $p->rooms_count,
                'is_verified' => $p->is_verified,
            ]);

        return Inertia::render('admin/properties/Index', [
            'properties' => $properties,
        ]);
    }

    public function toggleVerify(Property $property): RedirectResponse
    {
        $property->update(['is_verified' => ! $property->is_verified]);

        return back();
    }
}
