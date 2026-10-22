<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\PropertyBlock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    private const GERMAN_MONTHS = [
        1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April',
        5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember',
    ];

    public function index(Request $request)
    {
        $monthStart = $this->resolveMonthStart($request->input('month'));
        $monthEnd = $monthStart->copy()->endOfMonth();
        $daysInMonth = $monthStart->daysInMonth;

        $properties = Property::orderBy('title')->get(['id', 'slug', 'title', 'location']);

        $bookings = Booking::where('status', 'approved')
            ->whereDate('check_in', '<=', $monthEnd)
            ->whereDate('check_out', '>', $monthStart) // check-out / end date is the day after the last night
            ->get();

        $blocks = PropertyBlock::whereDate('start_date', '<=', $monthEnd)
            ->whereDate('end_date', '>', $monthStart)
            ->get();

        $rows = $properties->map(function (Property $property) use ($bookings, $blocks, $monthStart, $monthEnd, $daysInMonth) {
            $segments = collect();

            foreach ($bookings->where('property_id', $property->id) as $booking) {
                $segments->push($this->buildSegment($booking->check_in, $booking->check_out, $monthStart, $monthEnd, $daysInMonth, [
                    'type' => 'booking',
                    'label' => $booking->name,
                    'url' => route('admin.bookings.show', $booking),
                    'id' => $booking->id,
                ]));
            }

            foreach ($blocks->where('property_id', $property->id) as $block) {
                $segments->push($this->buildSegment($block->start_date, $block->end_date, $monthStart, $monthEnd, $daysInMonth, [
                    'type' => 'block',
                    'label' => $block->label ?: 'Gesperrt',
                    'id' => $block->id,
                ]));
            }

            return [
                'property' => $property,
                'segments' => $segments->sortBy('left')->values(),
            ];
        });

        $monthLabel = self::GERMAN_MONTHS[$monthStart->month] . ' ' . $monthStart->year;

        return view('admin.calendar.index', [
            'rows' => $rows,
            'properties' => $properties,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'daysInMonth' => $daysInMonth,
            'monthLabel' => $monthLabel,
            'monthValue' => $monthStart->format('Y-m'),
            'prevMonth' => $monthStart->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $monthStart->copy()->addMonth()->format('Y-m'),
            'todayDay' => now()->isSameMonth($monthStart) ? now()->day : null,
        ]);
    }

    public function storeBlock(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'label' => 'nullable|string|max:255',
            'month' => 'nullable|string',
        ], [
            'property_id.required' => 'Bitte wählen Sie eine Immobilie aus.',
            'start_date.required' => 'Bitte geben Sie ein Startdatum an.',
            'start_date.after_or_equal' => 'Das Startdatum darf nicht in der Vergangenheit liegen.',
            'end_date.required' => 'Bitte geben Sie ein Enddatum an.',
            'end_date.after' => 'Das Enddatum muss nach dem Startdatum liegen.',
        ]);

        if (
            Booking::hasOverlap((int) $validated['property_id'], $validated['start_date'], $validated['end_date'])
            || PropertyBlock::hasOverlap((int) $validated['property_id'], $validated['start_date'], $validated['end_date'])
        ) {
            return back()
                ->with('error', 'Dieser Zeitraum überschneidet sich mit einer bestehenden Buchung oder Sperre.')
                ->withInput();
        }

        PropertyBlock::create([
            'property_id' => $validated['property_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'label' => $validated['label'] ?? null,
        ]);

        $redirect = $validated['month'] ?? null;

        return redirect()
            ->route('admin.calendar.index', $redirect ? ['month' => $redirect] : [])
            ->with('success', 'Zeitraum wurde als belegt markiert.');
    }

    public function destroyBlock(Request $request, PropertyBlock $block)
    {
        $redirect = $request->input('month');
        $block->delete();

        return redirect()
            ->route('admin.calendar.index', $redirect ? ['month' => $redirect] : [])
            ->with('success', 'Sperre wurde entfernt.');
    }

    private function resolveMonthStart(?string $month): Carbon
    {
        if ($month) {
            try {
                return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            } catch (\Throwable) {
                // fall through to current month
            }
        }

        return now()->startOfMonth();
    }

    private function buildSegment(Carbon $start, Carbon $end, Carbon $monthStart, Carbon $monthEnd, int $daysInMonth, array $meta): array
    {
        // Midnight of the 1st of the next month. $monthEnd is endOfMonth() (23:59:59), so adding a day to it
        // directly would land on the 1st at 23:59:59 and stretch segments almost a full day past the grid.
        $monthBoundaryEnd = $monthEnd->copy()->startOfDay()->addDay();

        $clippedStart = $start->lt($monthStart) ? $monthStart->copy() : $start->copy();
        $clippedEnd = $end->gt($monthBoundaryEnd) ? $monthBoundaryEnd : $end->copy();

        $startOffset = $monthStart->diffInDays($clippedStart);
        $length = max($clippedStart->diffInDays($clippedEnd), 1);

        // Never let a bar reach past the last day of the month (it would widen the scroll area).
        $left = min(max($startOffset / $daysInMonth * 100, 0), 100);
        $width = min($length / $daysInMonth * 100, 100 - $left);

        return array_merge($meta, [
            'left' => round($left, 4),
            'width' => round($width, 4),
            'start' => $start->format('d.m.Y'),
            'end' => $end->format('d.m.Y'),
            'continues_before' => $start->lt($monthStart),
            'continues_after' => $end->gt($monthBoundaryEnd),
        ]);
    }
}
