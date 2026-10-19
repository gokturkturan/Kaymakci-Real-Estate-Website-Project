<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use App\Models\PropertyBlock;
use App\Support\BookingMailer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function getBookedDates(Property $property)
    {
        $bookedDates = array_values(array_unique(array_merge(
            Booking::getBookedDates($property->id),
            PropertyBlock::getBlockedDates($property->id)
        )));

        return response()->json([
            'booked_dates' => $bookedDates,
        ]);
    }

    public function store(Request $request, Property $property)
    {
        $validated = $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'guests' => 'required|integer|min:1|max:20',
            'message' => 'nullable|string|max:1000',
        ], [
            'check_in.required' => __('property.validation.check_in_required'),
            'check_out.required' => __('property.validation.check_out_required'),
            'check_out.after' => __('property.validation.check_out_after'),
            'name.required' => __('property.validation.name_required'),
            'email.required' => __('property.validation.email_required'),
            'email.email' => __('property.validation.email_email'),
            'guests.required' => __('property.validation.guests_required'),
        ]);

        // Check for overlapping bookings and manual (off-site) blocks
        if (
            Booking::hasOverlap($property->id, $validated['check_in'], $validated['check_out'])
            || PropertyBlock::hasOverlap($property->id, $validated['check_in'], $validated['check_out'])
        ) {
            return back()->with('error', __('booking.overlap_error'));
        }

        $nights = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));
        $pricePerPersonPerNight = (float) $property->price;

        $booking = Booking::create([
            'property_id' => $property->id,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'guests' => $validated['guests'],
            'price_per_person_per_night' => $pricePerPersonPerNight,
            'total_price' => $nights * $validated['guests'] * $pricePerPersonPerNight,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
            'locale' => app()->getLocale(),
        ]);

        BookingMailer::sendNewBookingNotification($booking);

        return back()->with('success', __('booking.success'));
    }
}
