@extends('layouts.app')

@php
    $decimalSep = in_array(app()->getLocale(), ['de', 'pl', 'sk', 'ro'], true) ? ',' : '.';
    $thousandSep = in_array(app()->getLocale(), ['de', 'pl', 'sk', 'ro'], true) ? '.' : ',';
    $priceFormatted = number_format($property->price, 2, $decimalSep, $thousandSep);

    // BCP-47 tag for Intl.NumberFormat, and the matching flatpickr locale key ('default' = English).
    $intlLocaleMap = ['de' => 'de-DE', 'en' => 'en-GB', 'pl' => 'pl-PL', 'sk' => 'sk-SK', 'ro' => 'ro-RO'];
    $flatpickrLocaleMap = ['de' => 'de', 'en' => 'default', 'pl' => 'pl', 'sk' => 'sk', 'ro' => 'ro'];
    $intlLocale = $intlLocaleMap[app()->getLocale()] ?? 'en-GB';
    $flatpickrLocale = $flatpickrLocaleMap[app()->getLocale()] ?? 'default';
@endphp

@section('title', __('property.meta_title', ['title' => $property->localized_title, 'bedrooms' => $property->bedrooms, 'area' => $property->area, 'location' => $property->location]))

@section('meta_description', __('property.meta_description', ['title' => $property->localized_title, 'location' => $property->location, 'bedrooms' => $property->bedrooms, 'bathrooms' => $property->bathrooms, 'area' => $property->area, 'price' => $priceFormatted, 'excerpt' => Str::limit($property->localized_description_plain, 100)]))

@section('meta_keywords', __('property.meta_keywords', ['title' => $property->localized_title, 'location' => $property->location, 'bedrooms' => $property->bedrooms]))

@section('og_type', 'product')
@section('og_title', __('property.og_title', ['title' => $property->localized_title, 'price' => $priceFormatted]))
@section('og_description', __('property.og_description', ['bedrooms' => $property->bedrooms, 'bathrooms' => $property->bathrooms, 'area' => $property->area, 'location' => $property->location]))
@section('og_image', $property->first_image ?? asset('images/og-default.jpg'))
@section('twitter_image', $property->first_image ?? asset('images/og-default.jpg'))

@section('structured_data')
@php
    $realEstateListing = [
        '@context' => 'https://schema.org',
        '@type' => 'RealEstateListing',
        'name' => $property->localized_title,
        'description' => $property->localized_description_plain,
        'url' => route('properties.show', $property),
        'datePosted' => $property->created_at->toIso8601String(),

        'image' => $property->images
            ->map(fn ($image) => url($image->url))
            ->values()
            ->toArray(),

        'offers' => [
            '@type' => 'Offer',
            'url' => route('properties.show', $property),
            'price' => $property->price,
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
        ],

        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $property->location,
            'addressCountry' => 'DE',
        ],

        'floorSize' => [
            '@type' => 'QuantitativeValue',
            'value' => $property->area,
            'unitCode' => 'MTK',
        ],

        'numberOfBedrooms' => $property->bedrooms,
        'numberOfBathroomsTotal' => $property->bathrooms,
    ];

    if ($property->latitude !== null && $property->longitude !== null) {
        $realEstateListing['geo'] = [
            '@type' => 'GeoCoordinates',
            'latitude' => $property->latitude,
            'longitude' => $property->longitude,
        ];
    }

    $breadcrumb = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => __('property.schema_home'),
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => __('property.schema_listings'),
                'item' => route('properties.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $property->localized_title,
                'item' => route('properties.show', $property),
            ],
        ],
    ];
@endphp

<script type="application/ld+json">
{!! json_encode($realEstateListing, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<script type="application/ld+json">
{!! json_encode($breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('content')
    <article class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" itemscope itemtype="https://schema.org/RealEstateListing">

        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-sm text-ivory-500">
                <li><a href="{{ route('properties.index') }}" class="hover:text-gold-700 transition-colors">{{ __('property.breadcrumb_home') }}</a></li>
                <li><span aria-hidden="true">/</span></li>
                <li><a href="{{ route('properties.index') }}" class="hover:text-gold-700 transition-colors">{{ __('property.breadcrumb_listings') }}</a></li>
                <li><span aria-hidden="true">/</span></li>
                <li class="text-ivory-900 font-medium" aria-current="page">{{ Str::limit($property->localized_title, 30) }}</li>
            </ol>
        </nav>

        <div class="bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200 overflow-hidden">
            {{-- Media Gallery (images + videos combined, in admin-defined order) --}}
            @php
                $media = $property->media;
            @endphp
            @if($media->count() > 0)
                <div class="relative" id="property-gallery" x-data="{ currentSlide: 0, totalSlides: {{ $media->count() }} }" x-effect="currentSlide, pauseInactiveVideos($el, currentSlide)">
                    {{-- Main Media --}}
                    <div class="aspect-video bg-ivory-200 overflow-hidden relative">
                        @foreach($media as $index => $item)
                            @if($item['type'] === 'image')
                                <img src="{{ $item['url'] }}"
                                     alt="{{ __('property.gallery_image_alt', ['title' => $property->localized_title, 'index' => $index + 1]) }}"
                                     class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                                     :class="currentSlide === {{ $index }} ? 'opacity-100' : 'opacity-0'"
                                     @if($index === 0) itemprop="image" @endif>
                            @else
                                <video src="{{ $item['url'] }}"
                                       class="gallery-slide absolute inset-0 w-full h-full object-contain bg-black transition-opacity duration-300"
                                       :class="currentSlide === {{ $index }} ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                                       data-type="video" data-index="{{ $index }}"
                                       controls preload="metadata" playsinline>
                                    {{ __('property.gallery_video_unsupported') }}
                                </video>
                            @endif
                        @endforeach

                        {{-- Navigation Arrows --}}
                        @if($media->count() > 1)
                            <button @click="currentSlide = (currentSlide - 1 + totalSlides) % totalSlides"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-navy-950/50 hover:bg-navy-950/70 text-white p-3 rounded-full transition"
                                    aria-label="{{ __('property.gallery_prev_aria') }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button @click="currentSlide = (currentSlide + 1) % totalSlides"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-navy-950/50 hover:bg-navy-950/70 text-white p-3 rounded-full transition"
                                    aria-label="{{ __('property.gallery_next_aria') }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            {{-- Counter --}}
                            <div class="absolute bottom-4 right-4 bg-navy-950/70 text-white text-sm px-3 py-1 rounded-full">
                                <span x-text="currentSlide + 1"></span> / {{ $media->count() }}
                            </div>
                        @endif
                    </div>

                    {{-- Thumbnail Navigation --}}
                    @if($media->count() > 1)
                        <div class="flex gap-2 p-4 bg-ivory-100 overflow-x-auto">
                            @foreach($media as $index => $item)
                                <button @click="currentSlide = {{ $index }}"
                                        class="relative flex-shrink-0 w-20 h-14 rounded overflow-hidden border-2 transition"
                                        :class="currentSlide === {{ $index }} ? 'border-gold-600' : 'border-transparent hover:border-ivory-300'">
                                    @if($item['type'] === 'image')
                                        <img src="{{ $item['url'] }}" alt="{{ __('property.gallery_thumbnail_alt', ['index' => $index + 1]) }}" class="w-full h-full object-cover">
                                    @else
                                        <video src="{{ $item['url'] }}" class="w-full h-full object-cover pointer-events-none" muted preload="metadata"></video>
                                        <span class="absolute inset-0 flex items-center justify-center bg-navy-950/40">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6.3 3.7a1 1 0 011.03-.05l9 5.5a1 1 0 010 1.7l-9 5.5A1 1 0 016 15.5v-11a1 1 0 01.3-.8z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                <script>
                    function pauseInactiveVideos(container, activeIndex) {
                        if (!container) return;
                        container.querySelectorAll('.gallery-slide[data-type="video"]').forEach((video) => {
                            if (parseInt(video.dataset.index, 10) !== activeIndex) {
                                video.pause();
                            }
                        });
                    }
                </script>
            @else
                <figure class="aspect-video bg-ivory-200 overflow-hidden">
                    <div class="w-full h-full flex items-center justify-center text-ivory-400" aria-label="{{ __('property.gallery_no_image') }}">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 22V12h6v10"/>
                        </svg>
                    </div>
                </figure>
            @endif

            <div class="p-5 sm:p-8">
                <header class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
                    <div>
                        <h1 class="font-serif text-2xl sm:text-4xl font-semibold text-ivory-900" itemprop="name">{{ $property->localized_title }}</h1>
                        <p class="text-ivory-500 mt-1" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                            <span itemprop="addressLocality">{{ $property->location }}</span>
                        </p>
                    </div>
                    <p class="font-serif text-3xl font-bold text-gold-700 whitespace-nowrap" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                        <span itemprop="price" content="{{ $property->price }}">{{ $priceFormatted }}</span>
                        <span itemprop="priceCurrency" content="EUR">€</span>
                        <span class="block text-sm font-medium text-ivory-500">{{ __('property.price_suffix_per_person_night') }}</span>
                        <meta itemprop="availability" content="https://schema.org/InStock">
                    </p>
                </header>

                <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 p-4 sm:p-6 bg-ivory-100 rounded-xl mb-8" aria-label="{{ __('property.stats_aria_label') }}">
                    <div class="text-center">
                        <p class="font-serif text-2xl font-bold text-ivory-900" itemprop="numberOfRooms">{{ $property->bedrooms }}</p>
                        <p class="text-sm text-ivory-500">{{ __('property.stats_rooms') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="font-serif text-2xl font-bold text-ivory-900" itemprop="numberOfBathroomsTotal">{{ $property->bathrooms }}</p>
                        <p class="text-sm text-ivory-500">{{ __('property.stats_bath') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="font-serif text-2xl font-bold text-ivory-900">{{ $property->area }}</p>
                        <p class="text-sm text-ivory-500">m²</p>
                        <meta itemprop="floorSize" content="{{ $property->area }} MTK">
                    </div>
                    @if($property->king_size_bed_count > 0)
                        <div class="text-center">
                            <p class="font-serif text-2xl font-bold text-ivory-900">{{ $property->king_size_bed_count }}</p>
                            <p class="text-sm text-ivory-500">{{ $property->king_size_bed_count > 1 ? __('property.stats_king_size_beds') : __('property.stats_king_size_bed') }}</p>
                        </div>
                    @endif
                    @if($property->single_bed_count > 0)
                        <div class="text-center">
                            <p class="font-serif text-2xl font-bold text-ivory-900">{{ $property->single_bed_count }}</p>
                            <p class="text-sm text-ivory-500">{{ $property->single_bed_count > 1 ? __('property.stats_single_beds') : __('property.stats_single_bed') }}</p>
                        </div>
                    @endif
                    <div class="text-center">
                        <p class="font-serif text-2xl font-bold text-ivory-900">{{ $property->has_parking ? __('property.stats_yes') : __('property.stats_no') }}</p>
                        <p class="text-sm text-ivory-500">{{ __('property.stats_parking') }}</p>
                    </div>
                </section>

                <section aria-labelledby="description-title">
                    <h2 id="description-title" class="font-serif text-xl sm:text-2xl font-semibold text-ivory-900 mb-3">{{ __('property.description_heading') }}</h2>
                    <div class="prose prose-neutral max-w-none text-ivory-700 leading-relaxed" itemprop="description">{!! $property->localized_description !!}</div>
                </section>

                @if($property->latitude !== null && $property->longitude !== null)
                    <section class="mt-8" aria-labelledby="map-title">
                        <h2 id="map-title" class="font-serif text-xl sm:text-2xl font-semibold text-ivory-900 mb-3">{{ __('property.map_heading') }}</h2>
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
                        <div id="property-map" class="h-96 rounded-xl border border-ivory-200" aria-label="{{ __('property.map_aria_label', ['location' => $property->location]) }}"></div>
                        <p class="text-xs text-ivory-500 mt-2">{{ __('property.map_attribution') }}</p>
                        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const coordinates = [{{ (float) $property->latitude }}, {{ (float) $property->longitude }}];
                                const map = L.map('property-map').setView(coordinates, 18);
                                const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                                    attribution: 'Tiles &copy; Esri — Source: Esri, Maxar, Earthstar Geographics, and the GIS User Community'
                                });
                                const streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    maxZoom: 19,
                                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                });

                                satellite.addTo(map);
                                streets.addTo(map);
                                L.marker(coordinates).addTo(map).bindPopup(@js($property->location));
                                L.control.layers({ {{ Illuminate\Support\Js::from(__('property.map_layer_satellite')) }}: satellite, {{ Illuminate\Support\Js::from(__('property.map_layer_streets')) }}: streets }).addTo(map);
                            });
                        </script>
                    </section>
                @endif

                {{-- Booking Section --}}
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                <style>
                    .flatpickr-day.booked {
                        background: #dc2626 !important;
                        color: white !important;
                        text-decoration: line-through;
                        cursor: not-allowed !important;
                    }
                    .flatpickr-day.booked:hover {
                        background: #b91c1c !important;
                    }
                    .flatpickr-day.selected {
                        background: #b8732e !important;
                        border-color: #b8732e !important;
                    }
                    .flatpickr-day.inRange {
                        background: #f5e1c6 !important;
                        border-color: #f5e1c6 !important;
                    }
                </style>
                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/de.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pl.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/sk.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ro.js"></script>
                <script>
                // Local calendar date as 'Y-m-d', without converting through UTC.
                // dateObj.toISOString() shifts the date back by one day for anyone in a
                // timezone ahead of UTC (e.g. Germany, Turkey) because it reads the UTC
                // date of a *local* midnight instant instead of the local calendar date.
                function toLocalYmd(date) {
                    const y = date.getFullYear();
                    const m = String(date.getMonth() + 1).padStart(2, '0');
                    const d = String(date.getDate()).padStart(2, '0');
                    return `${y}-${m}-${d}`;
                }

                // Parse a 'Y-m-d' string as a local-midnight Date (new Date('Y-m-d') parses
                // as UTC midnight instead, which drifts a day off toLocalYmd() above once the
                // visitor's timezone is ahead of UTC).
                function parseYmd(str) {
                    const [y, m, d] = str.split('-').map(Number);
                    return new Date(y, m - 1, d);
                }

                function bookingForm() {
                    return {
                        checkIn: '',
                        checkOut: '',
                        bookedDates: [],
                        dateError: '',
                        nights: 0,
                        guests: {{ old('guests', 1) }},
                        pricePerPersonPerNight: {{ (float) $property->price }},
                        loading: false,
                        isValid: false,
                        checkInPicker: null,
                        checkOutPicker: null,

                        get totalPrice() {
                            return this.nights * this.guests * this.pricePerPersonPerNight;
                        },

                        formatPrice(price) {
                            return new Intl.NumberFormat({{ Illuminate\Support\Js::from($intlLocale) }}, { style: 'currency', currency: 'EUR' }).format(price);
                        },

                        async init() {
                            await this.fetchBookedDates();
                            this.initDatePickers();
                        },

                        async fetchBookedDates() {
                            try {
                                const response = await fetch('{{ route('bookings.dates', $property) }}');
                                const data = await response.json();
                                this.bookedDates = data.booked_dates || [];
                            } catch (error) {
                                console.error('Error fetching booked dates:', error);
                                this.bookedDates = [];
                            }
                        },

                        initDatePickers() {
                            const self = this;
                            const today = toLocalYmd(new Date());

                            this.checkInPicker = flatpickr('#check_in', {
                                locale: {{ Illuminate\Support\Js::from($flatpickrLocale) }},
                                dateFormat: 'Y-m-d',
                                minDate: today,
                                disable: this.bookedDates,
                                onDayCreate: function(dObj, dStr, fp, dayElem) {
                                    const dateStr = toLocalYmd(dayElem.dateObj);
                                    if (self.bookedDates.includes(dateStr)) {
                                        dayElem.classList.add('booked');
                                    }
                                },
                                onChange: function(selectedDates, dateStr) {
                                    self.checkIn = dateStr;
                                    if (self.checkOutPicker) {
                                        self.checkOutPicker.set('minDate', dateStr);
                                    }
                                    self.validateDates();
                                }
                            });

                            this.checkOutPicker = flatpickr('#check_out', {
                                locale: {{ Illuminate\Support\Js::from($flatpickrLocale) }},
                                dateFormat: 'Y-m-d',
                                minDate: today,
                                disable: this.bookedDates,
                                onDayCreate: function(dObj, dStr, fp, dayElem) {
                                    const dateStr = toLocalYmd(dayElem.dateObj);
                                    if (self.bookedDates.includes(dateStr)) {
                                        dayElem.classList.add('booked');
                                    }
                                },
                                onChange: function(selectedDates, dateStr) {
                                    self.checkOut = dateStr;
                                    self.validateDates();
                                }
                            });
                        },

                        validateDates() {
                            this.dateError = '';
                            this.nights = 0;
                            this.isValid = false;

                            if (!this.checkIn || !this.checkOut) return;

                            const checkInDate = parseYmd(this.checkIn);
                            const checkOutDate = parseYmd(this.checkOut);

                            if (checkOutDate <= checkInDate) {
                                this.dateError = {{ Illuminate\Support\Js::from(__('property.booking_js_checkout_after_checkin')) }};
                                return;
                            }

                            // Calculate nights
                            const diffTime = Math.abs(checkOutDate - checkInDate);
                            this.nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                            // Check for conflicts with booked dates
                            const current = parseYmd(this.checkIn);
                            while (current < checkOutDate) {
                                const dateStr = toLocalYmd(current);
                                if (this.bookedDates.includes(dateStr)) {
                                    this.dateError = {{ Illuminate\Support\Js::from(__('property.booking_js_overlap')) }};
                                    return;
                                }
                                current.setDate(current.getDate() + 1);
                            }

                            // All validations passed
                            this.isValid = true;
                        }
                    }
                }
                </script>
                <section class="mt-8 p-5 sm:p-8 bg-gold-50 rounded-2xl" aria-label="{{ __('property.booking_heading') }}" x-data="bookingForm()">
                    <h3 class="font-serif text-lg sm:text-xl font-semibold text-ivory-900 mb-2">{{ __('property.booking_heading') }}</h3>
                    <p class="text-ivory-700 mb-4">{{ __('property.booking_subheading') }}</p>

                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('bookings.store', $property) }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Check-in Date --}}
                            <div>
                                <label for="check_in" class="block text-sm font-medium text-ivory-700 mb-1">{{ __('property.booking_checkin_label') }}</label>
                                <input type="text" id="check_in" name="check_in" required readonly
                                       placeholder="{{ __('property.booking_date_placeholder') }}"
                                       class="w-full px-4 py-3 border border-ivory-300 rounded-lg focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none bg-white cursor-pointer">
                                @error('check_in')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Check-out Date --}}
                            <div>
                                <label for="check_out" class="block text-sm font-medium text-ivory-700 mb-1">{{ __('property.booking_checkout_label') }}</label>
                                <input type="text" id="check_out" name="check_out" required readonly
                                       placeholder="{{ __('property.booking_date_placeholder') }}"
                                       class="w-full px-4 py-3 border border-ivory-300 rounded-lg focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none bg-white cursor-pointer">
                                @error('check_out')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Date validation message --}}
                        <div x-show="dateError" x-cloak class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                            <p x-text="dateError"></p>
                        </div>

                        {{-- Nights info --}}
                        <div x-show="nights > 0 && !dateError" x-cloak class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg">
                            <p><span x-text="nights"></span> {{ __('property.booking_nights_suffix') }}</p>
                            <p class="mt-1 font-semibold">{{ __('property.booking_total_price_label') }} <span x-text="formatPrice(totalPrice)"></span></p>
                            <p class="text-sm">{{ __('property.booking_price_per_person_night', ['price' => $priceFormatted . ' €']) }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-ivory-700 mb-1">{{ __('property.booking_name_label') }}</label>
                                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                                       class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                                       placeholder="{{ __('property.booking_name_placeholder') }}">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-ivory-700 mb-1">{{ __('property.booking_email_label') }}</label>
                                <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                       class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                                       placeholder="{{ __('property.booking_email_placeholder') }}">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-ivory-700 mb-1">{{ __('property.booking_phone_label') }}</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                       class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                                       placeholder="{{ __('property.booking_phone_placeholder') }}">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Guests --}}
                            <div>
                                <label for="guests" class="block text-sm font-medium text-ivory-700 mb-1">{{ __('property.booking_guests_label') }}</label>
                                <select id="guests" name="guests" required x-model.number="guests"
                                        class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('guests', 1) == $i ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? __('property.booking_guest_singular') : __('property.booking_guest_plural') }}</option>
                                    @endfor
                                </select>
                                @error('guests')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Message --}}
                        <div>
                            <label for="message" class="block text-sm font-medium text-ivory-700 mb-1">{{ __('property.booking_message_label') }}</label>
                            <textarea id="message" name="message" rows="3"
                                      class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                                      placeholder="{{ __('property.booking_message_placeholder') }}">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                :disabled="!isValid"
                                class="w-full bg-gold-600 text-ivory-50 px-6 py-3 rounded-lg font-medium hover:bg-gold-700 transition-colors disabled:bg-ivory-300 disabled:cursor-not-allowed shadow-soft">
                            {{ __('property.booking_submit') }}
                        </button>
                    </form>

                    <p class="text-sm text-ivory-500 mt-4">
                        {{ __('property.booking_disclaimer') }}
                    </p>
                </section>
            </div>
        </div>

        <meta itemprop="datePosted" content="{{ $property->created_at->toIso8601String() }}">
    </article>
@endsection
