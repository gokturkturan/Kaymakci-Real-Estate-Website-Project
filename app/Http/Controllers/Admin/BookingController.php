<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PropertyBlock;
use App\Support\BookingMailer;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('property')->orderBy('check_in', 'asc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected', 'cancelled')");
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('check_in', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('check_out', '<=', $request->to_date);
        }

        $bookings = $query->paginate(20)->withQueryString();

        $stats = [
            'pending' => Booking::where('status', 'pending')->count(),
            'approved' => Booking::where('status', 'approved')->count(),
            'rejected' => Booking::where('status', 'rejected')->count(),
            'total' => Booking::count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load('property');
        return view('admin.bookings.show', compact('booking'));
    }

    public function approve(Booking $booking)
    {
        if (PropertyBlock::hasOverlap($booking->property_id, $booking->check_in->toDateString(), $booking->check_out->toDateString())) {
            return back()->with('error', 'Dieser Zeitraum ist im Kalender manuell gesperrt. Bitte prüfen Sie die Sperre, bevor Sie bestätigen.');
        }

        $booking->update(['status' => 'approved']);

        BookingMailer::sendApproved($booking);

        return back()->with('success', 'Buchung wurde bestätigt.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        BookingMailer::sendRejected($booking);

        return back()->with('success', 'Buchung wurde abgelehnt.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Buchung wurde gelöscht.');
    }
}
