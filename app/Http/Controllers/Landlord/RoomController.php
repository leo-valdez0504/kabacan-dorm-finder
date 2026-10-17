<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store(Request $request, Property $property): RedirectResponse
    {
        abort_unless($property->landlord_id === $request->user()->id, 403);

        $data = $request->validate([
            'room_type' => ['required', 'in:single,shared,bedspace'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $property->rooms()->create([
            'room_type' => $data['room_type'],
            'capacity' => $data['capacity'],
            'available_slots' => $data['capacity'],
            'price' => $data['price'],
            'status' => 'available',
        ]);

        return back();
    }

    public function destroy(Request $request, Room $room): RedirectResponse
    {
        abort_unless($room->property->landlord_id === $request->user()->id, 403);

        $room->delete();

        return back();
    }
}
