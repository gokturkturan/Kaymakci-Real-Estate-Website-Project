@extends('admin.layouts.app')

@section('title', 'Buchungen verwalten')
@section('header', 'Buchungen')

@section('content')
    @php
        $statusColors = [
            'pending' => 'bg-amber-100 text-amber-800',
            'approved' => 'bg-emerald-100 text-emerald-800',
            'rejected' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-ivory-200 text-ivory-700',
        ];
    @endphp
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
            <p class="text-sm text-yellow-600">Ausstehend</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-2xl font-bold text-green-700">{{ $stats['approved'] }}</p>
            <p class="text-sm text-green-600">Bestätigt</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-2xl font-bold text-red-700">{{ $stats['rejected'] }}</p>
            <p class="text-sm text-red-600">Abgelehnt</p>
        </div>
        <div class="bg-ivory-100 border border-ivory-200 rounded-lg p-4">
            <p class="text-2xl font-bold text-ivory-800">{{ $stats['total'] }}</p>
            <p class="text-sm text-ivory-600">Gesamt</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200 p-4 mb-6">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap gap-4 lg:items-end">
            <div>
                <label for="status" class="block text-sm font-medium text-ivory-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full lg:w-auto px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 outline-none">
                    <option value="">Alle</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Ausstehend</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Bestätigt</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Abgelehnt</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Storniert</option>
                </select>
            </div>
            <div>
                <label for="from_date" class="block text-sm font-medium text-ivory-700 mb-1">Check-in ab</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                       class="w-full lg:w-auto px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 outline-none">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-ivory-700 mb-1">Check-out bis</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                       class="w-full lg:w-auto px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 outline-none">
            </div>
            <div class="flex items-center gap-3 sm:col-span-2 lg:col-span-1">
                <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-navy-800 text-ivory-50 rounded-lg hover:bg-navy-900 transition-colors font-medium">
                    Filtern
                </button>
                @if(request()->hasAny(['status', 'from_date', 'to_date']))
                    <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 text-ivory-600 hover:text-gold-700 transition-colors">
                        Filter zurücksetzen
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bookings List --}}
    <div class="bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200 overflow-hidden">
        @if($bookings->count() > 0)
            {{-- Phones, tablets & small laptops: one card per booking (the full table needs a wide screen) --}}
            <ul class="xl:hidden divide-y divide-ivory-200 md:divide-y-0 md:grid md:grid-cols-2 md:gap-4 md:p-4">
                @foreach($bookings as $booking)
                    <li class="p-4 md:rounded-xl md:border md:border-ivory-200 {{ $booking->status === 'pending' ? 'bg-amber-50' : 'md:bg-white' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium text-ivory-900">{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</p>
                                <p class="text-sm text-ivory-500">{{ $booking->nights }} Nächte · {{ $booking->guests }} {{ $booking->guests == 1 ? 'Gast' : 'Gäste' }}</p>
                            </div>
                            <span class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$booking->status] }}">
                                {{ $booking->status_label }}
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-1 min-[480px]:grid-cols-2 gap-3 text-sm">
                            <div class="min-w-0">
                                <p class="text-xs uppercase text-ivory-400 font-medium">Immobilie</p>
                                <a href="{{ route('admin.properties.edit', $booking->property) }}" class="text-gold-700 hover:text-gold-800 font-medium block truncate">
                                    {{ $booking->property->title }}
                                </a>
                                <p class="text-ivory-500 truncate">{{ $booking->property->location }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs uppercase text-ivory-400 font-medium">Kunde</p>
                                <p class="font-medium text-ivory-900 truncate">{{ $booking->name }}</p>
                                <a href="mailto:{{ $booking->email }}" class="text-ivory-500 hover:text-gold-700 block truncate">{{ $booking->email }}</a>
                                @if($booking->phone)
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $booking->phone) }}" class="text-ivory-500 hover:text-gold-700 block truncate">{{ $booking->phone }}</a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-ivory-200/70 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-ivory-900">{{ number_format($booking->display_total_price, 2, ',', '.') }} &euro;</p>
                                <p class="text-xs text-ivory-500 truncate">{{ number_format($booking->display_price_per_person_per_night, 2, ',', '.') }} &euro; / Person/Nacht</p>
                            </div>
                            @include('admin.bookings._actions', ['booking' => $booking])
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- Large screens: full table --}}
            <div class="hidden xl:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-ivory-100 border-b border-ivory-200">
                        <tr>
                            <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase">Zeitraum</th>
                            <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase">Immobilie</th>
                            <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase">Kunde</th>
                            <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase">Gäste</th>
                            <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase">Gesamtpreis</th>
                            <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase">Status</th>
                            <th class="px-4 2xl:px-6 py-3 text-right text-xs font-medium text-ivory-500 uppercase">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ivory-200">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-ivory-50/80 transition-colors {{ $booking->status === 'pending' ? 'bg-amber-50' : '' }}">
                                <td class="px-4 2xl:px-6 py-4 whitespace-nowrap">
                                    <p class="font-medium text-ivory-900">{{ $booking->check_in->format('d.m.Y') }} - {{ $booking->check_out->format('d.m.Y') }}</p>
                                    <p class="text-sm text-ivory-500">{{ $booking->nights }} Nächte</p>
                                </td>
                                <td class="px-4 2xl:px-6 py-4">
                                    <a href="{{ route('admin.properties.edit', $booking->property) }}"
                                       class="text-gold-700 hover:text-gold-800 font-medium">
                                        {{ Str::limit($booking->property->title, 30) }}
                                    </a>
                                    <p class="text-sm text-ivory-500">{{ $booking->property->location }}</p>
                                </td>
                                <td class="px-4 2xl:px-6 py-4">
                                    <p class="font-medium text-ivory-900">{{ $booking->name }}</p>
                                    <p class="text-sm text-ivory-500">{{ $booking->email }}</p>
                                    @if($booking->phone)
                                        <p class="text-sm text-ivory-500">{{ $booking->phone }}</p>
                                    @endif
                                </td>
                                <td class="px-4 2xl:px-6 py-4 whitespace-nowrap">
                                    <span class="text-ivory-900">{{ $booking->guests }}</span>
                                </td>
                                <td class="px-4 2xl:px-6 py-4 whitespace-nowrap">
                                    <p class="font-semibold text-ivory-900">{{ number_format($booking->display_total_price, 2, ',', '.') }} &euro;</p>
                                    <p class="text-sm text-ivory-500">{{ number_format($booking->display_price_per_person_per_night, 2, ',', '.') }} &euro; / Person/Nacht</p>
                                </td>
                                <td class="px-4 2xl:px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$booking->status] }}">
                                        {{ $booking->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 2xl:px-6 py-4 whitespace-nowrap text-right">
                                    @include('admin.bookings._actions', ['booking' => $booking])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 sm:px-6 py-4 border-t border-ivory-200">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-ivory-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-ivory-500">Keine Buchungen gefunden.</p>
            </div>
        @endif
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="fixed inset-0 bg-navy-950/60 hidden items-end sm:items-center justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-ivory-50 rounded-2xl shadow-lift p-5 sm:p-6 max-w-md w-full">
            <h3 class="font-serif text-lg font-semibold text-ivory-900 mb-4">Buchung ablehnen</h3>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="admin_notes" class="block text-sm font-medium text-ivory-700 mb-1">
                        Grund der Ablehnung (optional)
                    </label>
                    <textarea name="admin_notes" id="admin_notes" rows="3"
                              class="w-full px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 outline-none"
                              placeholder="z.B. Die Immobilie ist in diesem Zeitraum nicht verfügbar..."></textarea>
                    <p class="text-xs text-ivory-500 mt-1">Diese Nachricht wird dem Kunden per E-Mail mitgeteilt.</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="hideRejectModal()"
                            class="flex-1 px-4 py-2 border border-ivory-300 text-ivory-700 rounded-lg hover:bg-ivory-100 transition-colors font-medium">
                        Abbrechen
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        Ablehnen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal(bookingId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/admin/bookings/${bookingId}/reject`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRejectModal();
            }
        });
    </script>
@endsection
