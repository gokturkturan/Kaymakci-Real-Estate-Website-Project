@extends('admin.layouts.app')

@section('title', 'Kalender')
@section('header', 'Belegungskalender')

@section('content')
    {{-- Month navigation --}}
    <div class="bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200 p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.calendar.index', ['month' => $prevMonth]) }}"
               class="p-2 rounded-lg border border-ivory-200 text-ivory-600 hover:bg-ivory-100 transition-colors" aria-label="Vorheriger Monat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-serif text-lg sm:text-xl font-semibold text-navy-900 w-44 text-center">{{ $monthLabel }}</h2>
            <a href="{{ route('admin.calendar.index', ['month' => $nextMonth]) }}"
               class="p-2 rounded-lg border border-ivory-200 text-ivory-600 hover:bg-ivory-100 transition-colors" aria-label="Nächster Monat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="flex items-center gap-5">
            {{-- Legend --}}
            <div class="flex items-center gap-4 text-sm text-ivory-600">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-gold-500"></span>Website-Buchung</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-navy-700"></span>Manuell gesperrt</span>
            </div>
            @if(!$monthStart->isSameMonth(now()))
                <a href="{{ route('admin.calendar.index') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg border border-ivory-200 text-ivory-600 hover:bg-ivory-100 transition-colors text-sm font-medium whitespace-nowrap"
                   title="Zum aktuellen Monat zurückkehren">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Heute
                </a>
            @endif
            <form method="GET" action="{{ route('admin.calendar.index') }}">
                <input type="month" name="month" value="{{ $monthValue }}" onchange="this.form.submit()"
                       class="px-3 py-2 border border-ivory-300 rounded-lg bg-white text-sm focus:ring-2 focus:ring-gold-400 outline-none">
            </form>
        </div>
    </div>

    {{-- Hinweis, wenn eine per Klick begonnene Auswahl über den Monatswechsel hinweg läuft --}}
    <div id="pending-selection-banner" class="hidden bg-gold-50 border border-gold-200 text-gold-900 rounded-xl px-4 py-3 mb-6 flex items-center justify-between gap-4 text-sm">
        <span id="pending-selection-text"></span>
        <button type="button" id="pending-selection-cancel" class="text-gold-800 hover:text-gold-900 font-medium underline whitespace-nowrap flex-shrink-0">Abbrechen</button>
    </div>

    {{-- Add a manual block --}}
    <div id="manual-block-form" class="bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200 p-4 sm:p-6 mb-6">
        <h3 class="font-serif text-lg font-semibold text-navy-900 mb-1">Zeitraum manuell sperren</h3>
        <p class="text-sm text-ivory-500 mb-4">Für Buchungen außerhalb der Website (Telefon, Airbnb, Booking.com, …) – markiert die Tage hier im Kalender als belegt. Tipp: Klicken Sie im Kalender unten auf den ersten und den letzten Tag der Immobilie – direkt dort erscheint ein kleines Fenster für eine optionale Notiz.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.calendar.blocks.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap gap-4 lg:items-end">
            @csrf
            <input type="hidden" name="month" value="{{ $monthValue }}">

            <div class="lg:w-56">
                <label for="property_id" class="block text-sm font-medium text-ivory-700 mb-1">Immobilie</label>
                <select name="property_id" id="property_id" required
                        class="w-full px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 outline-none">
                    <option value="">Bitte wählen…</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->title }} ({{ $property->location }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-ivory-700 mb-1">Von</label>
                <input type="date" name="start_date" id="start_date" required min="{{ now()->format('Y-m-d') }}" value="{{ old('start_date') }}"
                       class="w-full lg:w-auto px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 outline-none">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-ivory-700 mb-1">Bis</label>
                <input type="date" name="end_date" id="end_date" required min="{{ now()->format('Y-m-d') }}" value="{{ old('end_date') }}"
                       class="w-full lg:w-auto px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 outline-none disabled:bg-ivory-100 disabled:text-ivory-400 disabled:cursor-not-allowed">
            </div>

            <div class="lg:w-56">
                <label for="label" class="block text-sm font-medium text-ivory-700 mb-1">Notiz (optional)</label>
                <input type="text" name="label" id="label" maxlength="255" value="{{ old('label') }}" placeholder="z.B. Airbnb – Herr Novak"
                       class="w-full px-3 py-2 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 outline-none">
            </div>

            <div>
                <button type="submit" class="px-5 py-2 bg-navy-800 text-ivory-50 rounded-lg hover:bg-navy-900 transition-colors font-medium whitespace-nowrap">
                    Zeitraum sperren
                </button>
            </div>
        </form>
    </div>

    {{-- Calendar grid --}}
    <div class="bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200 overflow-hidden">
        @if($properties->isEmpty())
            <div class="p-12 text-center">
                <p class="text-ivory-500">Noch keine Immobilien vorhanden.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <div class="min-w-[900px]">
                    {{-- Day header --}}
                    <div class="flex border-b border-ivory-200 bg-ivory-100">
                        <div class="w-48 flex-shrink-0 px-3 py-2 text-xs font-semibold text-ivory-500 uppercase flex items-center">Immobilie</div>
                        <div class="flex-1 flex">
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $dayDate = $monthStart->copy()->day($d);
                                    $isWeekend = $dayDate->isWeekend();
                                    $isToday = $todayDay === $d;
                                    $weekdayAbbr = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'][$dayDate->isoWeekday() - 1];
                                @endphp
                                <div class="flex-1 text-center text-[11px] py-1.5 leading-tight {{ $isToday ? 'bg-gold-100 text-gold-800 font-bold rounded' : ($isWeekend ? 'text-ivory-400' : 'text-ivory-500') }}">
                                    <div>{{ $d }}</div>
                                    <div class="text-[9px] uppercase tracking-wide opacity-70">{{ $weekdayAbbr }}</div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- Property rows --}}
                    @foreach($rows as $row)
                        <div class="flex border-b border-ivory-100 hover:bg-ivory-50/60 transition-colors">
                            <div class="w-48 flex-shrink-0 px-3 py-2.5 min-w-0">
                                <a href="{{ route('admin.properties.edit', $row['property']) }}" class="text-sm font-medium text-navy-900 hover:text-gold-700 transition-colors block truncate">
                                    {{ $row['property']->title }}
                                </a>
                                <p class="text-xs text-ivory-500 truncate">{{ $row['property']->location }}</p>
                            </div>
                            <div class="flex-1 relative h-12">
                                {{-- Clickable day cells: click the first day, then the last day to block; a small popup then asks for an optional note --}}
                                <div class="absolute inset-0 flex">
                                    @for($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $cellDate = $monthStart->copy()->day($d);
                                            $isPastCell = $cellDate->lt(today());
                                        @endphp
                                        <button type="button"
                                                @if($isPastCell) disabled @endif
                                                class="day-cell flex-1 {{ $cellDate->isWeekend() ? 'bg-ivory-100/70' : '' }} {{ $todayDay === $d ? 'bg-gold-50' : '' }} border-r border-ivory-100/60 transition-colors p-0 {{ $isPastCell ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gold-100/60 cursor-pointer' }}"
                                                data-property-id="{{ $row['property']->id }}"
                                                data-property-title="{{ $row['property']->title }}"
                                                data-date="{{ $cellDate->format('Y-m-d') }}"
                                                aria-label="{{ $cellDate->format('d.m.Y') }}"></button>
                                    @endfor
                                </div>

                                @foreach($row['segments'] as $seg)
                                    @if($seg['type'] === 'booking')
                                        <a href="{{ $seg['url'] }}"
                                           class="absolute inset-y-1.5 bg-gold-500/90 hover:bg-gold-600 text-ivory-50 text-[11px] font-medium flex items-center px-2 overflow-hidden shadow-soft {{ $seg['continues_before'] ? '' : 'rounded-l-md' }} {{ $seg['continues_after'] ? '' : 'rounded-r-md' }}"
                                           style="left: {{ $seg['left'] }}%; width: {{ $seg['width'] }}%;"
                                           title="{{ $seg['label'] }} ({{ $seg['start'] }} – {{ $seg['end'] }})">
                                            <span class="truncate">{{ $seg['label'] }}</span>
                                        </a>
                                    @else
                                        <div class="absolute inset-y-1.5 bg-navy-700/90 text-ivory-50 text-[11px] font-medium flex items-center gap-1 px-2 overflow-hidden shadow-soft {{ $seg['continues_before'] ? '' : 'rounded-l-md' }} {{ $seg['continues_after'] ? '' : 'rounded-r-md' }}"
                                             style="left: {{ $seg['left'] }}%; width: {{ $seg['width'] }}%;"
                                             title="{{ $seg['label'] }} ({{ $seg['start'] }} – {{ $seg['end'] }})">
                                            <span class="truncate flex-1">{{ $seg['label'] }}</span>
                                            <form action="{{ route('admin.calendar.blocks.destroy', $seg['id']) }}" method="POST"
                                                  class="flex-shrink-0 leading-none" onsubmit="return confirm('Sperre wirklich entfernen?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="month" value="{{ $monthValue }}">
                                                <button type="submit" class="text-ivory-300 hover:text-white" title="Sperre entfernen">&times;</button>
                                            </form>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Zeitraum per Klick im Kalender wählen: erster Klick = Starttag, zweiter Klick = Letzter Tag
         (frühere Tage werden danach gesperrt, man kann also nicht "zurück" klicken). Direkt im
         Kalender erscheint dann ein kleines Fenster für eine optionale Notiz, das das Formular
         oben ausfüllt und absendet – die Dropdown-/Datumsfelder müssen nicht mehr von Hand bedient werden. --}}
    <script>
        (function () {
            function pad(n) {
                return String(n).padStart(2, '0');
            }

            function addDays(ymd, days) {
                const [y, m, d] = ymd.split('-').map(Number);
                const dt = new Date(y, m - 1, d);
                dt.setDate(dt.getDate() + days);
                return `${dt.getFullYear()}-${pad(dt.getMonth() + 1)}-${pad(dt.getDate())}`;
            }

            function formatDisplay(ymd) {
                const [y, m, d] = ymd.split('-');
                return `${d}.${m}.${y}`;
            }

            const PENDING_KEY = 'calendarBlockSelection';

            function savePendingSelection(sel) {
                try {
                    if (sel) {
                        sessionStorage.setItem(PENDING_KEY, JSON.stringify({
                            propertyId: sel.propertyId,
                            propertyTitle: sel.propertyTitle,
                            date: sel.date,
                        }));
                    } else {
                        sessionStorage.removeItem(PENDING_KEY);
                    }
                } catch (e) {
                    // sessionStorage unavailable (private mode etc.) – selection just won't survive a month change
                }
            }

            function loadPendingSelection() {
                try {
                    const raw = sessionStorage.getItem(PENDING_KEY);
                    return raw ? JSON.parse(raw) : null;
                } catch (e) {
                    return null;
                }
            }

            // "Bis" ist gesperrt, bis "Von" gewählt ist, und kann danach kein Datum vor/an "Von" annehmen.
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');

            function syncEndDateConstraint() {
                if (!startInput.value) {
                    endInput.value = '';
                    endInput.disabled = true;
                    return;
                }

                endInput.disabled = false;
                const minEnd = addDays(startInput.value, 1);
                endInput.min = minEnd;
                if (endInput.value && endInput.value < minEnd) {
                    endInput.value = '';
                }
            }

            startInput.addEventListener('change', syncEndDateConstraint);
            syncEndDateConstraint();

            const startHighlight = ['ring-2', 'ring-inset', 'ring-gold-500'];
            const rangeHighlight = ['bg-gold-200/70'];

            let selStart = null; // { propertyId, propertyTitle, date, el, rowEl }

            const todayYmd = (function () {
                const n = new Date();
                return `${n.getFullYear()}-${pad(n.getMonth() + 1)}-${pad(n.getDate())}`;
            })();

            function clearRowState(rowEl) {
                rowEl.querySelectorAll('.day-cell').forEach(function (c) {
                    c.classList.remove(...startHighlight, ...rangeHighlight);
                    // Leave past-day cells exactly as the server rendered them (permanently disabled).
                    if (c.dataset.date < todayYmd) {
                        return;
                    }
                    c.disabled = false;
                    c.classList.remove('opacity-30', 'cursor-not-allowed');
                    c.classList.add('cursor-pointer');
                });
                rowEl.querySelectorAll('.block-popup').forEach(function (p) {
                    p.remove();
                });
            }

            function updateBanner() {
                const banner = document.getElementById('pending-selection-banner');
                const text = document.getElementById('pending-selection-text');
                if (!banner || !text) {
                    return;
                }
                if (selStart) {
                    text.textContent = 'Auswahl läuft: ' + selStart.propertyTitle + ' – ab ' + formatDisplay(selStart.date) +
                        '. Wählen Sie jetzt den letzten Tag (auch in einem anderen Monat möglich).';
                    banner.classList.remove('hidden');
                } else {
                    banner.classList.add('hidden');
                }
            }

            function resetSelection() {
                if (selStart) {
                    clearRowState(selStart.rowEl);
                }
                selStart = null;
                savePendingSelection(null);
                updateBanner();
            }

            function disableEarlierCells(rowEl, fromDate) {
                rowEl.querySelectorAll('.day-cell').forEach(function (c) {
                    if (c.dataset.date < fromDate) {
                        c.disabled = true;
                        c.classList.add('opacity-30', 'cursor-not-allowed');
                        c.classList.remove('cursor-pointer');
                    }
                });
            }

            function highlightRange(rowEl, fromDate, toDate) {
                rowEl.querySelectorAll('.day-cell').forEach(function (c) {
                    if (c.dataset.date >= fromDate && c.dataset.date <= toDate) {
                        c.classList.add(...rangeHighlight);
                    }
                });
            }

            function showLabelPopup(rowEl, startCell, endCell, propertyId, startDate, lastDay) {
                rowEl.querySelectorAll('.block-popup').forEach(function (p) {
                    p.remove();
                });

                const rowRect = rowEl.getBoundingClientRect();
                const endRect = endCell.getBoundingClientRect();
                // startCell can be null if the start day belongs to a month that isn't shown anymore.
                const startRect = startCell ? startCell.getBoundingClientRect() : endRect;
                const centerLeft = ((Math.min(startRect.left, endRect.left) + Math.max(startRect.right, endRect.right)) / 2) - rowRect.left;

                const popup = document.createElement('div');
                popup.className = 'block-popup absolute z-20 top-full mt-2 bg-white border border-ivory-300 rounded-lg shadow-lift p-3 flex items-center gap-2 whitespace-nowrap';
                popup.style.left = centerLeft + 'px';
                popup.style.transform = 'translateX(-50%)';
                popup.innerHTML =
                    '<input type="text" maxlength="255" placeholder="Notiz (optional), z.B. Airbnb" ' +
                    'class="popup-label-input px-2 py-1.5 border border-ivory-300 rounded-md text-sm w-48 focus:ring-2 focus:ring-gold-400 outline-none">' +
                    '<button type="button" class="popup-confirm px-3 py-1.5 bg-navy-800 text-ivory-50 rounded-md text-sm font-medium hover:bg-navy-900 whitespace-nowrap">Sperren</button>' +
                    '<button type="button" class="popup-cancel px-2 py-1.5 text-ivory-500 hover:text-ivory-700 text-sm">Abbrechen</button>';

                rowEl.appendChild(popup);

                const input = popup.querySelector('.popup-label-input');
                input.focus();

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        popup.querySelector('.popup-confirm').click();
                    } else if (e.key === 'Escape') {
                        popup.querySelector('.popup-cancel').click();
                    }
                });

                popup.querySelector('.popup-confirm').addEventListener('click', function () {
                    document.getElementById('property_id').value = propertyId;
                    document.getElementById('start_date').value = startDate;
                    document.getElementById('end_date').disabled = false;
                    document.getElementById('end_date').value = addDays(lastDay, 1);
                    document.getElementById('label').value = input.value;
                    savePendingSelection(null);
                    document.getElementById('manual-block-form').querySelector('form').submit();
                });

                popup.querySelector('.popup-cancel').addEventListener('click', function () {
                    resetSelection();
                });
            }

            document.querySelectorAll('.day-cell').forEach(function (cell) {
                cell.addEventListener('click', function () {
                    const propertyId = cell.dataset.propertyId;
                    const propertyTitle = cell.dataset.propertyTitle;
                    const date = cell.dataset.date;
                    const rowEl = cell.closest('.relative');

                    // No selection yet, or switched to a different property's row: (re)start here.
                    if (!selStart || selStart.propertyId !== propertyId) {
                        resetSelection();
                        selStart = { propertyId: propertyId, propertyTitle: propertyTitle, date: date, el: cell, rowEl: rowEl };
                        cell.classList.add(...startHighlight);
                        disableEarlierCells(rowEl, date);
                        savePendingSelection(selStart);
                        updateBanner();
                        return;
                    }

                    // Clicked the same cell again: cancel the selection.
                    if (selStart.date === date) {
                        resetSelection();
                        return;
                    }

                    // Earlier cells are disabled above, so "date" here is always after selStart.date.
                    const startDate = selStart.date;
                    const lastDay = date;
                    const startCell = selStart.el;

                    highlightRange(rowEl, startDate, lastDay);
                    showLabelPopup(rowEl, startCell, cell, propertyId, startDate, lastDay);
                });
            });

            const bannerCancelBtn = document.getElementById('pending-selection-cancel');
            if (bannerCancelBtn) {
                bannerCancelBtn.addEventListener('click', function () {
                    resetSelection();
                });
            }

            // Restore a selection that was started last month (or earlier) and carried over via
            // sessionStorage, so clicking "Von"-day in one month and the end day in a later month works.
            (function restorePendingSelection() {
                const pending = loadPendingSelection();
                if (!pending) {
                    return;
                }

                const anyCellForProperty = document.querySelector('.day-cell[data-property-id="' + pending.propertyId + '"]');
                if (!anyCellForProperty) {
                    savePendingSelection(null);
                    return;
                }

                const rowEl = anyCellForProperty.closest('.relative');
                const startCell = rowEl.querySelector('.day-cell[data-date="' + pending.date + '"]') || null;

                selStart = {
                    propertyId: pending.propertyId,
                    propertyTitle: pending.propertyTitle,
                    date: pending.date,
                    el: startCell,
                    rowEl: rowEl,
                };

                if (startCell) {
                    startCell.classList.add(...startHighlight);
                }
                disableEarlierCells(rowEl, pending.date);
                updateBanner();
            })();
        })();
    </script>
@endsection
