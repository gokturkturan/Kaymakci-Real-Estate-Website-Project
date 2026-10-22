@extends('admin.layouts.app')

@section('title', 'Immobilie bearbeiten')
@section('header', 'Immobilie bearbeiten')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-ivory-50 rounded-2xl shadow-soft border border-ivory-200 p-4 sm:p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css">
            <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
            <style>
                /* Quill's own CSS sets .ql-container/.ql-editor to height:100%, which
                   resolves unpredictably inside an auto-height CSS grid row and can make
                   the editor collapse or overflow onto the fields below it. Pin it to an
                   explicit, scrollable height instead. */
                #description-editor.ql-container { height: auto; }
                #description-editor .ql-editor { height: 180px; max-height: 320px; overflow-y: auto; }
            </style>

            <form id="property-form" action="{{ route('admin.properties.update', $property) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-ivory-700 mb-1">Titel *</label>
                        <input type="text" id="title" name="title" required value="{{ old('title', $property->title) }}"
                               class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                               placeholder="z.B. Villa mit Seeblick">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description-editor" class="block text-sm font-medium text-ivory-700 mb-1">Beschreibung *</label>
                        <div id="description-editor" class="bg-white"></div>
                        <p id="description-error" class="hidden text-sm text-red-600 mt-1">Bitte geben Sie eine Beschreibung ein.</p>
                        <textarea id="description" name="description" class="hidden">{{ old('description', $property->description) }}</textarea>
                        <p class="text-xs text-ivory-500 mt-1">Titel &amp; Beschreibung werden beim Speichern automatisch ins Englische, Polnische, Slowakische und Rumänische übersetzt – dafür gibt es keine eigenen Felder. Formatierung (fett, kursiv, Schriftgröße) wird nur auf Deutsch angezeigt; in den übersetzten Sprachen erscheint die Beschreibung als Fließtext.</p>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-ivory-700 mb-1">Preis pro Person / Nacht (EUR) *</label>
                        <input type="number" id="price" name="price" required value="{{ old('price', $property->price) }}" min="0" step="0.01"
                               class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                               placeholder="z.B. 99,00">
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-ivory-700 mb-1">Standort *</label>
                        <input type="text" id="location" name="location" required value="{{ old('location', $property->location) }}"
                               class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                               placeholder="z.B. Mainzer Landstraße 50, 60329 Frankfurt am Main">
                    </div>

                    <div>
                        <label for="bedrooms" class="block text-sm font-medium text-ivory-700 mb-1">Zimmer *</label>
                        <input type="number" id="bedrooms" name="bedrooms" required value="{{ old('bedrooms', $property->bedrooms) }}" min="0"
                               class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                               placeholder="z.B. 4">
                    </div>

                    <div>
                        <label for="bathrooms" class="block text-sm font-medium text-ivory-700 mb-1">Badezimmer *</label>
                        <input type="number" id="bathrooms" name="bathrooms" required value="{{ old('bathrooms', $property->bathrooms) }}" min="0"
                               class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                               placeholder="z.B. 2">
                    </div>

                    <div>
                        <label for="area" class="block text-sm font-medium text-ivory-700 mb-1">Fläche (m²) *</label>
                        <input type="number" id="area" name="area" required value="{{ old('area', $property->area) }}" min="0" step="0.5"
                               class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                               placeholder="z.B. 150">
                    </div>

                    <div>
                        <label for="king_size_bed_count" class="block text-sm font-medium text-ivory-700 mb-1">King Size Betten</label>
                        <input type="number" id="king_size_bed_count" name="king_size_bed_count" required value="{{ old('king_size_bed_count', $property->king_size_bed_count) }}" min="0"
                               class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                               placeholder="z.B. 1">
                    </div>

                    <div>
                        <label for="single_bed_count" class="block text-sm font-medium text-ivory-700 mb-1">Einzelbetten</label>
                        <input type="number" id="single_bed_count" name="single_bed_count" required value="{{ old('single_bed_count', $property->single_bed_count) }}" min="0"
                               class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                               placeholder="z.B. 2">
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" id="has_parking" name="has_parking" value="1" {{ old('has_parking', $property->has_parking) ? 'checked' : '' }}
                                   class="w-5 h-5 text-gold-600 border-ivory-300 rounded focus:ring-2 focus:ring-gold-400">
                            <span class="text-sm font-medium text-ivory-700">Parkplatz vorhanden</span>
                        </label>
                    </div>

                    <!-- Existing Media (images + videos, shared order) -->
                    @php
                        $existingMedia = $property->images
                            ->map(fn ($image) => ['type' => 'image', 'id' => $image->id, 'url' => $image->url, 'order' => $image->order])
                            ->concat($property->videos->map(fn ($video) => ['type' => 'video', 'id' => $video->id, 'url' => $video->url, 'order' => $video->order]))
                            ->sortBy('order')
                            ->values();
                    @endphp
                    @if($existingMedia->count() > 0)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-ivory-700 mb-2">Vorhandene Medien ({{ $existingMedia->count() }})</label>
                            <p class="text-sm text-ivory-500 mb-3">Ziehen Sie Bilder und Videos in die gewünschte Reihenfolge. Das erste Bild wird als Titelbild verwendet.</p>
                            <div id="existing-media-order"></div>
                            <div id="existing-media" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                                @foreach($existingMedia as $item)
                                    <div class="existing-media-card relative rounded-xl overflow-hidden shadow-soft border border-ivory-200 bg-ivory-50 p-2 cursor-move"
                                         data-media-type="{{ $item['type'] }}" data-media-id="{{ $item['id'] }}" draggable="true"
                                         ondragstart="startExistingMediaDrag(event, this)" ondragend="endExistingMediaDrag(this)" ondragover="event.preventDefault()" ondrop="dropExistingMedia(event, this)">
                                        <div class="relative aspect-square">
                                            @if($item['type'] === 'image')
                                                <img src="{{ $item['url'] }}" alt="Bild" class="w-full h-full object-cover rounded-md">
                                            @else
                                                <video src="{{ $item['url'] }}" class="w-full h-full object-cover rounded-md" controls muted></video>
                                            @endif
                                            <span class="media-position absolute top-2 left-2 bg-gold-600 text-ivory-50 text-xs font-semibold px-2 py-1 rounded"></span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 mt-2">
                                            <button type="button" onclick="moveExistingMedia(this.closest('.existing-media-card'), -1)" class="flex-1 px-2 py-1 text-sm border border-ivory-300 rounded hover:bg-white" title="Nach links verschieben">←</button>
                                            <button type="button" onclick="moveExistingMedia(this.closest('.existing-media-card'), 1)" class="flex-1 px-2 py-1 text-sm border border-ivory-300 rounded hover:bg-white" title="Nach rechts verschieben">→</button>
                                            @if($item['type'] === 'image')
                                                <button type="button" onclick="deleteImage({{ $item['id'] }}, '{{ csrf_token() }}')" class="col-span-2 px-2 py-1 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50" title="Bild löschen">Löschen</button>
                                            @else
                                                <button type="button" onclick="deleteVideo({{ $item['id'] }}, '{{ csrf_token() }}')" class="col-span-2 px-2 py-1 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50" title="Video löschen">Löschen</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add New Media -->
                    <div class="md:col-span-2">
                        <label for="media-input" class="block text-sm font-medium text-ivory-700 mb-1">Neue Bilder &amp; Videos hinzufügen</label>
                        <div class="border-2 border-dashed border-ivory-300 rounded-xl p-6 text-center hover:border-gold-400 transition-colors cursor-pointer bg-ivory-50/50"
                             onclick="document.getElementById('media-input').click()">
                            <svg class="w-12 h-12 text-ivory-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <p class="text-ivory-600">Klicken Sie hier oder ziehen Sie Bilder/Videos hierher</p>
                            <p class="text-sm text-ivory-400 mt-1">JPEG, PNG, WebP (max. 5MB) · MP4, MOV, WebM (max. 100MB)</p>
                            <input type="file" id="media-input" multiple accept="image/*,video/mp4,video/quicktime,video/webm" class="hidden"
                                   onchange="handleMediaInput(this)">
                            <input type="file" id="images" name="images[]" multiple class="hidden">
                            <input type="file" id="videos" name="videos[]" multiple class="hidden">
                            <div id="media-type-order"></div>
                        </div>
                        <p class="text-sm text-ivory-500 mt-3">Neue Medien werden nach den vorhandenen Medien angehängt. Ziehen Sie sie hier in die gewünschte Reihenfolge untereinander.</p>
                        <div id="media-preview" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 mt-4"></div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-4 pt-4 border-t border-ivory-200">
                    <a href="{{ route('admin.properties.index') }}"
                       class="px-6 py-3 border border-ivory-300 rounded-lg text-ivory-700 hover:bg-ivory-100 transition-colors text-center font-medium">
                        Abbrechen
                    </a>
                    <button type="submit" id="submit-btn"
                            class="px-6 py-3 bg-gold-600 text-ivory-50 rounded-lg hover:bg-gold-700 transition-colors font-medium shadow-soft inline-flex items-center justify-center gap-2">
                        <svg id="submit-spinner" class="hidden animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span id="submit-btn-text">Änderungen speichern</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- Existing media (already saved) reordering ---
        let draggedExistingMedia = null;

        function syncExistingMediaOrder() {
            const orderContainer = document.getElementById('existing-media-order');
            const cards = document.querySelectorAll('#existing-media .existing-media-card');
            if (!orderContainer) return;

            orderContainer.innerHTML = '';
            let imageCounter = 0;
            let videoCounter = 0;

            cards.forEach((card) => {
                const type = card.dataset.mediaType;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'media_order[]';
                input.value = `${type}-${card.dataset.mediaId}`;
                orderContainer.appendChild(input);

                const badge = card.querySelector('.media-position');
                if (type === 'image') {
                    imageCounter++;
                    badge.textContent = imageCounter === 1 ? 'Titelbild' : `Bild ${imageCounter}`;
                } else {
                    videoCounter++;
                    badge.textContent = `Video ${videoCounter}`;
                }
            });
        }

        function startExistingMediaDrag(event, card) {
            draggedExistingMedia = card;
            event.dataTransfer.effectAllowed = 'move';
            card.classList.add('opacity-50');
        }

        function dropExistingMedia(event, targetCard) {
            event.preventDefault();
            if (draggedExistingMedia && draggedExistingMedia !== targetCard) {
                const cards = Array.from(targetCard.parentNode.children);
                const sourceIndex = cards.indexOf(draggedExistingMedia);
                const targetIndex = cards.indexOf(targetCard);

                targetCard.parentNode.insertBefore(
                    draggedExistingMedia,
                    sourceIndex < targetIndex ? targetCard.nextElementSibling : targetCard
                );
                syncExistingMediaOrder();
            }
            if (draggedExistingMedia) draggedExistingMedia.classList.remove('opacity-50');
            draggedExistingMedia = null;
        }

        function endExistingMediaDrag(card) {
            card.classList.remove('opacity-50');
            draggedExistingMedia = null;
        }

        function moveExistingMedia(card, direction) {
            const sibling = direction < 0 ? card.previousElementSibling : card.nextElementSibling;
            if (!sibling) return;

            card.parentNode.insertBefore(card, direction < 0 ? sibling : sibling.nextElementSibling);
            syncExistingMediaOrder();
        }

        function deleteImage(imageId, token) {
            if (!confirm('Bild wirklich löschen?')) {
                return;
            }

            fetch('/admin/images/' + imageId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Fehler beim Löschen des Bildes.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Fehler beim Löschen des Bildes.');
            });
        }

        function deleteVideo(videoId, token) {
            if (!confirm('Video wirklich löschen?')) {
                return;
            }

            fetch('/admin/videos/' + videoId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Fehler beim Löschen des Videos.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Fehler beim Löschen des Videos.');
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            syncExistingMediaOrder();
        });

        // --- New media (not yet uploaded) selection, ordering & preview ---
        let selectedMedia = []; // { type: 'image'|'video', file: File }
        let draggedMediaIndex = null;

        function handleMediaInput(input) {
            const existingKeys = new Set(selectedMedia.map(item => `${item.file.name}-${item.file.size}-${item.file.lastModified}`));
            Array.from(input.files).forEach((file) => {
                const key = `${file.name}-${file.size}-${file.lastModified}`;
                if (!existingKeys.has(key)) {
                    selectedMedia.push({ type: file.type.startsWith('video/') ? 'video' : 'image', file });
                    existingKeys.add(key);
                }
            });
            input.value = '';
            syncMediaInputs();
            renderMediaPreviews();
        }

        function moveMedia(fromIndex, toIndex) {
            if (toIndex < 0 || toIndex >= selectedMedia.length || fromIndex === toIndex) {
                return;
            }

            const [item] = selectedMedia.splice(fromIndex, 1);
            selectedMedia.splice(toIndex, 0, item);
            syncMediaInputs();
            renderMediaPreviews();
        }

        function removeSelectedMedia(index) {
            selectedMedia.splice(index, 1);
            syncMediaInputs();
            renderMediaPreviews();
        }

        function syncMediaInputs() {
            const imageTransfer = new DataTransfer();
            const videoTransfer = new DataTransfer();

            selectedMedia.forEach((item) => {
                if (item.type === 'image') {
                    imageTransfer.items.add(item.file);
                } else {
                    videoTransfer.items.add(item.file);
                }
            });

            document.getElementById('images').files = imageTransfer.files;
            document.getElementById('videos').files = videoTransfer.files;

            const orderContainer = document.getElementById('media-type-order');
            orderContainer.innerHTML = '';
            selectedMedia.forEach((item) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'media_type_order[]';
                input.value = item.type;
                orderContainer.appendChild(input);
            });
        }

        function renderMediaPreviews() {
            const preview = document.getElementById('media-preview');
            preview.innerHTML = '';

            selectedMedia.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'relative rounded-lg border border-ivory-200 bg-ivory-50 p-2 cursor-move';
                card.draggable = true;

                card.addEventListener('dragstart', () => {
                    draggedMediaIndex = index;
                    card.classList.add('opacity-50');
                });
                card.addEventListener('dragend', () => {
                    draggedMediaIndex = null;
                    card.classList.remove('opacity-50');
                });
                card.addEventListener('dragover', (event) => event.preventDefault());
                card.addEventListener('drop', (event) => {
                    event.preventDefault();
                    if (draggedMediaIndex !== null) {
                        moveMedia(draggedMediaIndex, index);
                    }
                });

                let mediaEl;
                if (item.type === 'image') {
                    mediaEl = document.createElement('img');
                    mediaEl.src = URL.createObjectURL(item.file);
                    mediaEl.alt = `Neue Bildvorschau ${index + 1}`;
                    mediaEl.className = 'w-full h-32 object-cover rounded-md';
                } else {
                    mediaEl = document.createElement('video');
                    mediaEl.src = URL.createObjectURL(item.file);
                    mediaEl.className = 'w-full h-32 object-cover rounded-md';
                    mediaEl.muted = true;
                    mediaEl.controls = true;
                }

                const badge = document.createElement('span');
                badge.className = 'absolute top-3 left-3 bg-gold-600 text-white text-xs font-semibold px-2 py-1 rounded';
                badge.textContent = item.type === 'video' ? 'Video' : 'Bild';

                const controls = document.createElement('div');
                controls.className = 'grid grid-cols-2 gap-2 mt-2';

                const moveLeft = document.createElement('button');
                moveLeft.type = 'button';
                moveLeft.className = 'flex-1 px-2 py-1 text-sm border border-ivory-300 rounded hover:bg-white disabled:opacity-40';
                moveLeft.textContent = '←';
                moveLeft.title = 'Nach links verschieben';
                moveLeft.disabled = index === 0;
                moveLeft.addEventListener('click', () => moveMedia(index, index - 1));

                const moveRight = document.createElement('button');
                moveRight.type = 'button';
                moveRight.className = 'flex-1 px-2 py-1 text-sm border border-ivory-300 rounded hover:bg-white disabled:opacity-40';
                moveRight.textContent = '→';
                moveRight.title = 'Nach rechts verschieben';
                moveRight.disabled = index === selectedMedia.length - 1;
                moveRight.addEventListener('click', () => moveMedia(index, index + 1));

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'col-span-2 px-2 py-1 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50';
                removeButton.textContent = 'Löschen';
                removeButton.title = 'Aus Auswahl entfernen';
                removeButton.addEventListener('click', () => removeSelectedMedia(index));

                controls.append(moveLeft, moveRight, removeButton);
                card.append(mediaEl, badge, controls);
                preview.appendChild(card);
            });
        }

        const descriptionTextarea = document.getElementById('description');
        const descriptionQuill = new Quill('#description-editor', {
            theme: 'snow',
            placeholder: 'Ausführliche Beschreibung der Immobilie...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ size: ['small', false, 'large', 'huge'] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['clean'],
                ],
            },
        });
        if (descriptionTextarea.value) {
            descriptionQuill.root.innerHTML = descriptionTextarea.value;
        }

        // The real <textarea> is hidden (Quill is the visible editor), so it can't carry the browser's
        // "required" check – an empty hidden required field blocks the submit with
        // "An invalid form control ... is not focusable". Keep it in sync and validate the editor ourselves.
        const descriptionError = document.getElementById('description-error');
        const descriptionEditorEl = document.getElementById('description-editor');

        function syncDescription() {
            const isEmpty = descriptionQuill.getText().trim() === '';
            descriptionTextarea.value = isEmpty ? '' : descriptionQuill.root.innerHTML;
            return !isEmpty;
        }

        descriptionQuill.on('text-change', function () {
            if (syncDescription()) {
                descriptionError.classList.add('hidden');
                descriptionEditorEl.classList.remove('ring-2', 'ring-red-400');
            }
        });

        document.getElementById('property-form').addEventListener('submit', function (event) {
            if (!syncDescription()) {
                event.preventDefault();
                descriptionError.classList.remove('hidden');
                descriptionEditorEl.classList.add('ring-2', 'ring-red-400');
                descriptionEditorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                descriptionQuill.focus();
                return;
            }

            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            document.getElementById('submit-spinner').classList.remove('hidden');
            document.getElementById('submit-btn-text').textContent = 'Wird gespeichert – Übersetzung läuft...';
        });
    </script>
@endsection
