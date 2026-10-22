@extends('layouts.app')

@section('title', __('home.meta_title'))

@section('canonical', route('properties.index', request()->only('page')))

@section('meta_description', __('home.meta_description', ['count' => $properties->total()]))

@section('meta_keywords', __('home.meta_keywords'))

@section('og_title', __('home.og_title'))
@section('og_description', __('home.og_description'))

@section('structured_data')
@php
$itemList = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => __('home.schema_item_list_name'),
    'description' => __('home.schema_item_list_description'),
    'numberOfItems' => $properties->total(),
    'itemListElement' => $properties->map(function($property, $index) use ($properties) {
        $item = [
            '@type' => 'ListItem',
            'position' => (($properties->currentPage() - 1) * $properties->perPage()) + $index + 1,
            'item' => [
                '@type' => 'RealEstateListing',
                'name' => $property->localized_title,
                'description' => Str::limit($property->localized_description_plain, 160),
                'url' => route('properties.show', $property),
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $property->price,
                    'priceCurrency' => 'EUR',
                    'priceSpecification' => [
                        '@type' => 'UnitPriceSpecification',
                        'price' => $property->price,
                        'priceCurrency' => 'EUR',
                        'unitText' => __('home.schema_unit_text')
                    ]
                ]
            ]
        ];
        if ($property->image) {
            $item['item']['image'] = $property->image;
        }
        return $item;
    })->toArray()
];

$breadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __('home.schema_home'),
            'item' => url('/')
        ]
    ]
];
@endphp
<script type="application/ld+json">{!! json_encode($itemList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
    @php
        $decimalSep = in_array(app()->getLocale(), ['de', 'pl', 'sk', 'ro'], true) ? ',' : '.';
        $thousandSep = in_array(app()->getLocale(), ['de', 'pl', 'sk', 'ro'], true) ? '.' : ',';
    @endphp
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-950 via-navy-900 to-navy-800 text-ivory-50 py-16 sm:py-24 lg:py-28" aria-labelledby="hero-title">
        <div class="pointer-events-none absolute -top-32 right-[-6rem] w-[32rem] h-[32rem] rounded-full bg-gold-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-24 w-[26rem] h-[26rem] rounded-full bg-navy-500/20 blur-3xl"></div>
        <div class="relative max-w-7xl 2xl:max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block w-10 h-px bg-gold-400 mb-5"></span>
            <h1 id="hero-title" class="font-serif text-3xl sm:text-5xl 2xl:text-6xl font-semibold mb-4 tracking-tight text-balance max-w-4xl mx-auto">{{ __('home.hero_title') }}</h1>
            <p class="text-base sm:text-xl text-ivory-300 max-w-2xl mx-auto">{{ __('home.hero_subtitle') }}</p>
        </div>
    </section>

    {{-- Filter --}}
    <section class="max-w-7xl 2xl:max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        @php
            $selectedLocations = (array) request('location', []);
            $hasActiveFilters = request()->anyFilled(['location', 'price_min', 'price_max', 'bedrooms', 'bathrooms', 'area_min', 'area_max', 'parking']);
        @endphp
        <form method="GET" action="{{ route('properties.index') }}" class="bg-ivory-50 rounded-2xl shadow-lift border border-ivory-200 p-4 sm:p-6"
              x-data="{ filtersOpen: {{ $hasActiveFilters ? 'true' : 'false' }} }">
            {{-- Phones: the filter fields are folded away behind this toggle so the listings are visible right away --}}
            <button type="button" @click="filtersOpen = !filtersOpen" :aria-expanded="filtersOpen" aria-controls="property-filter-fields"
                    class="sm:hidden w-full flex items-center justify-between gap-3 text-left font-medium text-ivory-800">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    {{ __('home.filter_toggle') }}
                    @if($hasActiveFilters)
                        <span class="w-2 h-2 rounded-full bg-gold-500" aria-hidden="true"></span>
                    @endif
                </span>
                <svg class="w-5 h-5 text-ivory-400 transition-transform" :class="filtersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div id="property-filter-fields" class="{{ $hasActiveFilters ? 'grid mt-4 sm:mt-0' : 'hidden sm:grid' }} grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
                 :class="filtersOpen ? '!grid mt-4 sm:mt-0' : '!hidden sm:!grid'">
            <div class="relative" x-data="{ open: false, selected: {{ Illuminate\Support\Js::from(array_values($selectedLocations)) }} }" @click.outside="open = false">
                <label class="block text-sm font-medium text-ivory-700 mb-1">{{ __('home.filter_location_label') }}</label>
                <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between gap-2 px-3 py-2 border border-ivory-300 rounded-lg bg-white text-left focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none">
                    <span class="truncate text-ivory-700" x-text="selected.length ? selected.length + ' {{ __('home.filter_location_selected_suffix') }}' : '{{ __('home.filter_location_all') }}'"></span>
                    <svg class="w-4 h-4 text-ivory-400 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-transition style="display: none;"
                     class="absolute z-20 mt-1 w-full max-h-60 overflow-y-auto bg-white border border-ivory-300 rounded-lg shadow-lift">
                    @forelse($locations as $location)
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-ivory-100 cursor-pointer text-sm text-ivory-700">
                            <input type="checkbox" name="location[]" value="{{ $location }}" x-model="selected"
                                   class="w-4 h-4 text-gold-600 border-ivory-300 rounded focus:ring-2 focus:ring-gold-400">
                            {{ $location }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-sm text-ivory-400">{{ __('home.filter_location_empty') }}</p>
                    @endforelse
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-ivory-700 mb-1">{{ __('home.filter_price_label') }}</label>
                <div class="flex gap-2">
                    <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="{{ __('home.filter_from') }}" min="0"
                           class="w-1/2 px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none">
                    <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="{{ __('home.filter_to') }}" min="0"
                           class="w-1/2 px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none">
                </div>
            </div>

            <div>
                <label for="bedrooms" class="block text-sm font-medium text-ivory-700 mb-1">{{ __('home.filter_bedrooms_label') }}</label>
                <select id="bedrooms" name="bedrooms" class="w-full px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none">
                    <option value="">{{ __('home.filter_any') }}</option>
                    @for($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" {{ (string) request('bedrooms') === (string) $i ? 'selected' : '' }}>{{ $i }}+</option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="bathrooms" class="block text-sm font-medium text-ivory-700 mb-1">{{ __('home.filter_bathrooms_label') }}</label>
                <select id="bathrooms" name="bathrooms" class="w-full px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none">
                    <option value="">{{ __('home.filter_any') }}</option>
                    @for($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" {{ (string) request('bathrooms') === (string) $i ? 'selected' : '' }}>{{ $i }}+</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-ivory-700 mb-1">{{ __('home.filter_area_label') }}</label>
                <div class="flex gap-2">
                    <input type="number" name="area_min" value="{{ request('area_min') }}" placeholder="{{ __('home.filter_from') }}" min="0"
                           class="w-1/2 px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none">
                    <input type="number" name="area_max" value="{{ request('area_max') }}" placeholder="{{ __('home.filter_to') }}" min="0"
                           class="w-1/2 px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none">
                </div>
            </div>

            <div class="flex items-end">
                <label class="flex items-center gap-2 cursor-pointer select-none py-2">
                    <input type="checkbox" name="parking" value="1" {{ request('parking') ? 'checked' : '' }}
                           class="w-5 h-5 text-gold-600 border-ivory-300 rounded focus:ring-2 focus:ring-gold-400">
                    <span class="text-sm font-medium text-ivory-700">{{ __('home.filter_parking_only') }}</span>
                </label>
            </div>

            <div class="sm:col-span-2 lg:col-span-2 flex flex-col-reverse sm:flex-row sm:items-end sm:justify-end gap-3">
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gold-600 text-ivory-50 rounded-lg hover:bg-gold-700 transition-colors font-medium shadow-soft">
                    {{ __('home.filter_submit') }}
                </button>
                @if($hasActiveFilters)
                    <a href="{{ route('properties.index') }}" class="w-full sm:w-auto text-center px-6 py-2.5 border border-ivory-300 rounded-lg text-ivory-700 hover:bg-ivory-100 transition-colors">
                        {{ __('home.filter_reset') }}
                    </a>
                @endif
            </div>
            </div>
        </form>
    </section>

    {{-- Angebotsliste --}}
    <section class="max-w-7xl 2xl:max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16" aria-labelledby="listings-title">
        <h2 id="listings-title" class="font-serif text-2xl sm:text-3xl font-semibold text-navy-900 mb-6 sm:mb-8">{{ __('home.listings_heading', ['count' => $properties->total()]) }}</h2>

        @if($properties->isEmpty())
            <p class="text-ivory-500 text-center py-12">{{ __('home.listings_empty') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-6 lg:gap-8">
                @foreach($properties as $property)
                    <article class="group bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200 overflow-hidden hover:shadow-lift hover:-translate-y-1 transition-all duration-300" itemscope itemtype="https://schema.org/RealEstateListing">
                        <a href="{{ route('properties.show', $property) }}" class="block" itemprop="url">
                            <div class="aspect-video bg-ivory-200 overflow-hidden relative">
                                @if($property->first_image)
                                    <img src="{{ $property->first_image }}"
                                         alt="{{ __('home.card_image_alt', ['title' => $property->localized_title, 'bedrooms' => $property->bedrooms, 'area' => $property->area, 'location' => $property->location]) }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                         loading="lazy"
                                         itemprop="image">
                                    @if($property->images->count() > 1)
                                        <span class="absolute bottom-2 right-2 bg-navy-950/75 text-ivory-50 text-xs px-2 py-1 rounded">
                                            {{ __('home.card_photos_count', ['count' => $property->images->count()]) }}
                                        </span>
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-ivory-400" aria-label="{{ __('home.card_no_image') }}">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 22V12h6v10"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5">
                                <h3 class="font-serif text-lg font-semibold text-navy-900 group-hover:text-gold-700 transition-colors" itemprop="name">{{ $property->localized_title }}</h3>
                                <p class="text-sm text-ivory-500 mt-1" itemprop="address">{{ $property->location }}</p>
                                <p class="text-xl font-bold text-gold-700 mt-3" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                    <span itemprop="price" content="{{ $property->price }}">{{ number_format($property->price, 2, $decimalSep, $thousandSep) }}</span>
                                    <span itemprop="priceCurrency" content="EUR">€</span>
                                    <span class="text-sm font-medium text-ivory-500">{{ __('home.card_per_person_night') }}</span>
                                </p>
                                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3 text-sm text-ivory-500">
                                    <span><strong class="text-ivory-700">{{ $property->bedrooms }}</strong> {{ __('home.card_rooms') }}</span>
                                    <span><strong class="text-ivory-700">{{ $property->bathrooms }}</strong> {{ __('home.card_bath') }}</span>
                                    <span><strong class="text-ivory-700">{{ $property->area }}</strong> m²</span>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($properties->hasPages())
                <nav class="mt-12 flex justify-center" aria-label="{{ __('home.pagination_aria') }}">
                    <div class="flex flex-wrap justify-center items-center gap-2">
                        {{-- Previous --}}
                        @if($properties->onFirstPage())
                            <span class="min-w-[2.75rem] h-11 inline-flex items-center justify-center px-3 sm:px-4 text-ivory-400 bg-ivory-100 rounded-lg cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $properties->previousPageUrl() }}" class="min-w-[2.75rem] h-11 inline-flex items-center justify-center px-3 sm:px-4 text-ivory-700 bg-ivory-50 border border-ivory-200 rounded-lg shadow-soft hover:bg-gold-50 transition-colors" aria-label="{{ __('home.pagination_prev') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                            @if($page == $properties->currentPage())
                                <span class="min-w-[2.75rem] h-11 inline-flex items-center justify-center px-3 sm:px-4 text-ivory-50 bg-gold-600 rounded-lg font-medium" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="min-w-[2.75rem] h-11 inline-flex items-center justify-center px-3 sm:px-4 text-ivory-700 bg-ivory-50 border border-ivory-200 rounded-lg shadow-soft hover:bg-gold-50 transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if($properties->hasMorePages())
                            <a href="{{ $properties->nextPageUrl() }}" class="min-w-[2.75rem] h-11 inline-flex items-center justify-center px-3 sm:px-4 text-ivory-700 bg-ivory-50 border border-ivory-200 rounded-lg shadow-soft hover:bg-gold-50 transition-colors" aria-label="{{ __('home.pagination_next') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <span class="min-w-[2.75rem] h-11 inline-flex items-center justify-center px-3 sm:px-4 text-ivory-400 bg-ivory-100 rounded-lg cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                </nav>

                <p class="text-center text-sm text-ivory-500 mt-4">
                    {{ __('home.pagination_summary', ['current' => $properties->currentPage(), 'last' => $properties->lastPage(), 'total' => $properties->total()]) }}
                </p>
            @endif
        @endif
    </section>

    {{-- SEO Text Section --}}
    <section class="py-12 sm:py-16" aria-labelledby="seo-title">
        <div class="max-w-7xl 2xl:max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="seo-title" class="font-serif text-2xl font-semibold text-navy-900 mb-4">{{ __('home.seo_heading') }}</h2>
            <div class="prose prose-neutral max-w-4xl">
                <p class="text-ivory-700 leading-relaxed">
                    {!! __('home.seo_paragraph_1') !!}
                </p>
                <p class="text-ivory-700 leading-relaxed mt-4">
                    {{ __('home.seo_paragraph_2') }}
                </p>
                <p class="font-serif text-lg text-navy-800 mt-6 pt-5 border-t border-ivory-200">
                    <span class="text-gold-600">{{ __('home.seo_tagline') }}</span>
                </p>
            </div>
        </div>
    </section>
@endsection
