<?php

namespace App\Exports;

use App\Models\Reservation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReservationsExport implements FromCollection, WithHeadings
{
    public function __construct(private int $landlordId)
    {
    }

    public function headings(): array
    {
        return ['Student', 'Email', 'Property', 'Room type', 'Price', 'Move-in date', 'Status', 'Requested on'];
    }

    public function collection(): Collection
    {
        return Reservation::query()
            ->whereHas('room.property', fn ($q) => $q->where('landlord_id', $this->landlordId))
            ->with(['student', 'room.property'])
            ->latest()
            ->get()
            ->map(fn (Reservation $r) => [
                $r->student->name,
                $r->student->email,
                $r->room->property->name,
                $r->room->room_type,
                $r->room->price,
                $r->move_in_date->format('Y-m-d'),
                $r->status,
                $r->created_at->format('Y-m-d H:i'),
            ]);
    }
}