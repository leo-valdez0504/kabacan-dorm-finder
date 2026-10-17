<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .muted { color: #666; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Reservation Report</h1>
    <div class="muted">Landlord: {{ $landlord }} · Generated {{ $generatedAt }}</div>

    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Property</th>
                <th>Room</th>
                <th>Price</th>
                <th>Move-in</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $r)
                <tr>
                    <td>{{ $r->student->name }}</td>
                    <td>{{ $r->room->property->name }}</td>
                    <td>{{ ucfirst($r->room->room_type) }}</td>
                    <td>₱{{ number_format($r->room->price, 2) }}</td>
                    <td>{{ $r->move_in_date->format('M d, Y') }}</td>
                    <td>{{ ucfirst($r->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No reservations yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>