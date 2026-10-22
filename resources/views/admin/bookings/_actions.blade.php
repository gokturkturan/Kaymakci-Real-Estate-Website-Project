{{-- Approve / reject / details / delete buttons for one booking (used by the table and the mobile cards) --}}
<div class="flex items-center justify-end gap-1 sm:gap-2">
    @if($booking->status === 'pending')
        <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                    title="Bestätigen">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </form>
        <button type="button"
                onclick="showRejectModal({{ $booking->id }})"
                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                title="Ablehnen">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
    <a href="{{ route('admin.bookings.show', $booking) }}"
       class="p-2 text-ivory-600 hover:bg-ivory-100 rounded-lg transition-colors"
       title="Details">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
    </a>
    <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline"
          onsubmit="return confirm('Buchung wirklich löschen?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="p-2 text-ivory-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                title="Löschen">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    </form>
</div>
