@extends('layouts.app')

@section('title', __('about.meta_title'))

@section('meta_description', __('about.meta_description'))

@section('meta_keywords', __('about.meta_keywords'))

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-950 via-navy-900 to-navy-800 text-ivory-50 py-16 sm:py-24 lg:py-28">
        <div class="pointer-events-none absolute -top-32 right-[-6rem] w-[32rem] h-[32rem] rounded-full bg-gold-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-24 w-[26rem] h-[26rem] rounded-full bg-navy-500/20 blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block w-10 h-px bg-gold-400 mb-5"></span>
            <h1 class="font-serif text-3xl sm:text-5xl font-semibold mb-4 tracking-tight">{{ __('about.hero_title') }}</h1>
            <p class="text-lg sm:text-xl text-ivory-300">{{ __('about.hero_subtitle') }}</p>
        </div>
    </section>

    {{-- Content --}}
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200 p-6 sm:p-10">

            <div class="prose prose-lg max-w-none">
                <h2 class="font-serif text-2xl sm:text-3xl font-semibold text-ivory-900 mb-4">{{ __('about.heading') }}</h2>

                <p class="text-ivory-700 leading-relaxed mb-6">
                    {{ __('about.paragraph_1') }}
                </p>

                <p class="text-ivory-700 leading-relaxed mb-6">
                    {{ __('about.paragraph_2') }}
                </p>

                <p class="text-ivory-700 leading-relaxed mb-6">
                    {{ __('about.paragraph_3') }}
                </p>

                <p class="text-ivory-700 leading-relaxed mb-6">
                    {{ __('about.paragraph_4') }}
                </p>

                <p class="font-serif text-lg text-navy-800 mt-2 pt-5 border-t border-ivory-200">
                    <span class="text-gold-600">{{ __('about.paragraph_5') }}</span>
                </p>
            </div>

            {{-- CTA --}}
            <div class="mt-10 p-6 sm:p-8 bg-gold-50 rounded-xl text-center">
                <h3 class="font-serif text-lg sm:text-xl font-semibold text-ivory-900 mb-2">{{ __('about.cta_heading') }}</h3>
                <p class="text-ivory-700 mb-5">{{ __('about.cta_text') }}</p>
                <a href="{{ route('pages.contact') }}"
                   class="inline-flex items-center gap-2 bg-gold-600 text-ivory-50 px-6 py-3 rounded-lg font-medium hover:bg-gold-700 transition-colors shadow-soft">
                    {{ __('about.cta_button') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
@endsection
