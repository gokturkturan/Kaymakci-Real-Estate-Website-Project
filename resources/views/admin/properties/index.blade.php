@extends('admin.layouts.app')

@section('title', 'Immobilien')
@section('header', 'Immobilien verwalten')

@section('content')
    <div class="bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200">
        <div class="px-4 sm:px-6 py-4 border-b border-ivory-200 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-serif text-lg font-semibold text-ivory-900">Alle Immobilien ({{ $properties->total() }})</h2>
            <a href="{{ route('admin.properties.create') }}"
               class="w-full sm:w-auto bg-gold-600 text-ivory-50 px-4 py-2 rounded-lg hover:bg-gold-700 transition-colors flex items-center justify-center gap-2 font-medium shadow-soft">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Neue Immobilie
            </a>
        </div>

        {{-- Phones, tablets & small laptops: card list --}}
        <ul class="xl:hidden divide-y divide-ivory-200">
            @forelse($properties as $property)
                <li class="p-4 flex gap-3 sm:gap-4">
                    <a href="{{ route('admin.properties.edit', $property) }}" class="flex-shrink-0">
                        @if($property->first_image)
                            <img src="{{ $property->first_image }}" alt="{{ $property->title }}" class="w-20 h-16 sm:w-28 sm:h-20 object-cover rounded-lg">
                        @else
                            <div class="w-20 h-16 sm:w-28 sm:h-20 bg-ivory-200 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ivory-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </a>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('admin.properties.edit', $property) }}" class="font-medium text-ivory-900 hover:text-gold-700 block truncate">{{ $property->title }}</a>
                        <p class="text-sm text-ivory-500 truncate">{{ $property->location }}</p>
                        <p class="text-xs text-ivory-500 mt-0.5">
                            {{ $property->bedrooms }} Zi. &middot; {{ $property->bathrooms }} Bad &middot; {{ $property->area }} m&sup2; &middot; {{ $property->images->count() }} Bilder
                        </p>
                        <div class="mt-1 flex items-center justify-between gap-2">
                            <p class="text-sm min-w-0">
                                <span class="font-semibold text-ivory-900">{{ number_format($property->price, 2, ',', '.') }} &euro;</span>
                                <span class="text-ivory-500">/ Person/Nacht</span>
                            </p>
                            <div class="-mr-2 flex-shrink-0">
                                @include('admin.properties._actions', ['property' => $property])
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-ivory-500">Noch keine Immobilien vorhanden.</li>
            @endforelse
        </ul>

        {{-- Large screens: full table --}}
        <div class="hidden xl:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-ivory-100">
                    <tr>
                        <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase tracking-wider">Bild</th>
                        <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase tracking-wider">Titel</th>
                        <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase tracking-wider">Standort</th>
                        <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase tracking-wider">Preis pro Person / Nacht</th>
                        <th class="px-4 2xl:px-6 py-3 text-left text-xs font-medium text-ivory-500 uppercase tracking-wider">Details</th>
                        <th class="px-4 2xl:px-6 py-3 text-right text-xs font-medium text-ivory-500 uppercase tracking-wider">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ivory-200">
                    @forelse($properties as $property)
                        <tr class="hover:bg-ivory-50/80 transition-colors">
                            <td class="px-4 2xl:px-6 py-4">
                                @if($property->first_image)
                                    <img src="{{ $property->first_image }}" alt="{{ $property->title }}" class="w-16 h-12 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-12 bg-ivory-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-ivory-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 2xl:px-6 py-4">
                                <div class="font-medium text-ivory-900">{{ $property->title }}</div>
                                <div class="text-sm text-ivory-500">{{ $property->images->count() }} Bilder</div>
                            </td>
                            <td class="px-4 2xl:px-6 py-4 text-ivory-600">{{ $property->location }}</td>
                            <td class="px-4 2xl:px-6 py-4 font-semibold text-ivory-900">{{ number_format($property->price, 2, ',', '.') }} &euro; <span class="text-sm font-normal text-ivory-500">/ Person/Nacht</span></td>
                            <td class="px-4 2xl:px-6 py-4 text-sm text-ivory-600">
                                {{ $property->bedrooms }} Zi. &middot; {{ $property->bathrooms }} Bad &middot; {{ $property->area }} m&sup2;
                            </td>
                            <td class="px-4 2xl:px-6 py-4 text-right">
                                @include('admin.properties._actions', ['property' => $property])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-ivory-500">
                                Noch keine Immobilien vorhanden.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($properties->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-ivory-200">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
@endsection
