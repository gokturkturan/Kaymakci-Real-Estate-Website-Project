{{-- View / edit / delete buttons for one property (used by the table and the mobile cards) --}}
<div class="flex items-center justify-end gap-1">
    <a href="{{ route('properties.show', $property) }}" target="_blank"
       class="p-2 rounded-lg text-ivory-500 hover:text-gold-700 hover:bg-ivory-100 transition-colors" title="Ansehen">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
    </a>
    <a href="{{ route('admin.properties.edit', $property) }}"
       class="p-2 rounded-lg text-ivory-500 hover:text-gold-700 hover:bg-ivory-100 transition-colors" title="Bearbeiten">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
    </a>
    <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" class="inline"
          onsubmit="return confirm('Sind Sie sicher, dass Sie diese Immobilie löschen möchten?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="p-2 rounded-lg text-ivory-500 hover:text-red-600 hover:bg-red-50 transition-colors" title="Löschen">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    </form>
</div>
